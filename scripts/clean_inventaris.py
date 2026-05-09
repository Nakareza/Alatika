import argparse
import csv
import json
import re
import unicodedata
from pathlib import Path


ALIASES = {
    "nama_alat": ["nama_alat", "nama barang", "nama_barang", "nama", "barang", "item", "alat", "uraian", "deskripsi"],
    "kategori": ["kategori", "jenis", "kelompok", "group", "category"],
    "kode_barang": ["kode_barang", "kode barang", "kode", "kode_alat", "serial number", "serial", "sn", "mac", "barcode"],
    "jumlah_stok": ["jumlah", "qty", "quantity", "stok", "stock", "jumlah stok"],
    "lokasi_simpan": ["lokasi", "lokasi_simpan", "rak", "lemari", "ruang", "tempat"],
    "tahun_perolehan": ["tahun", "tahun_perolehan", "year", "thn"],
    "kondisi": ["kondisi", "status", "keadaan"],
    "perlengkapan_detail": ["perlengkapan", "kelengkapan", "aksesoris", "accessories", "detail", "detail_kelengkapan"],
    "is_borrowable": ["is_borrowable", "borrowable", "bisa_pinjam", "bisa dipinjam", "status_pinjaman"],
}


def normalize(value):
    value = unicodedata.normalize("NFKC", str(value or "")).strip()
    return re.sub(r"\s+", " ", value)


def norm_key(value):
    value = normalize(value).lower().replace("_", " ")
    value = re.sub(r"[^\w\s/+-]", " ", value)
    return re.sub(r"\s+", " ", value).strip()


def first_value(row, keys):
    for key in keys:
        key = norm_key(key)
        for row_key, value in row.items():
            if norm_key(row_key) == key and normalize(value):
                return normalize(value)
    return ""


def parse_int(value, default=1):
    value = normalize(value)
    match = re.search(r"-?\d+", value.replace(".", "").replace(",", ""))
    return int(match.group()) if match else default


def parse_year(value):
    year = parse_int(value, 0)
    return year if 1900 <= year <= 2100 else ""


def parse_bool(value, fallback=None):
    value = normalize(value).lower()
    if not value:
        return fallback
    if value in {"1", "true", "yes", "ya", "borrowable", "bisa pinjam", "dipinjam"}:
        return True
    if value in {"0", "false", "no", "tidak", "aset statis", "statis", "non borrowable"}:
        return False
    return fallback


def parse_detail(value):
    value = normalize(value)
    if not value:
        return ""
    parts = [part.strip() for part in re.split(r"[;,|/]+", value) if part.strip()]
    return json.dumps(parts, ensure_ascii=False) if parts else ""


def is_static_asset(text):
    text = normalize(text).lower()
    keywords = ["ac", "proyektor", "projector", "fasilitas ruangan", "aset statis", "instalasi", "terpasang", "fixed", "permanen"]
    return any(keyword in text for keyword in keywords)


def normalize_row(row, source_file, source_row):
    nama_alat = first_value(row, ALIASES["nama_alat"]) or "UNKNOWN"
    kategori = first_value(row, ALIASES["kategori"]) or "Umum"
    kode_barang = first_value(row, ALIASES["kode_barang"])
    jumlah_stok = max(1, parse_int(first_value(row, ALIASES["jumlah_stok"]), 1))
    lokasi_simpan = first_value(row, ALIASES["lokasi_simpan"])
    tahun_perolehan = parse_year(first_value(row, ALIASES["tahun_perolehan"]))
    kondisi = first_value(row, ALIASES["kondisi"]) or "Baik"
    perlengkapan_detail = parse_detail(first_value(row, ALIASES["perlengkapan_detail"]))
    borrowable = parse_bool(first_value(row, ALIASES["is_borrowable"]), None)
    if borrowable is None:
        borrowable = not is_static_asset(f"{nama_alat} {kategori} {kode_barang}")

    return {
        "source_file": source_file,
        "source_row": source_row,
        "nama_alat": nama_alat,
        "kategori": kategori,
        "kode_barang": kode_barang,
        "jumlah_stok": jumlah_stok,
        "lokasi_simpan": lokasi_simpan,
        "tahun_perolehan": tahun_perolehan,
        "kondisi": kondisi,
        "perlengkapan_detail": perlengkapan_detail,
        "is_borrowable": 1 if borrowable else 0,
    }


def read_csv(path):
    raw = path.read_text(encoding="utf-8-sig", errors="ignore")
    sample = raw[:4096]
    delimiters = [",", ";", "\t", "|"]
    delimiter = max(delimiters, key=lambda d: sample.count(d))
    with path.open("r", encoding="utf-8-sig", newline="") as handle:
        reader = csv.DictReader(handle, delimiter=delimiter)
        return list(reader)


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("input_dir", help="Folder berisi CSV mentah")
    parser.add_argument("output_file", help="Path output master_inventaris.csv")
    args = parser.parse_args()

    input_dir = Path(args.input_dir)
    rows_out = []

    for file_path in sorted(input_dir.rglob("*.csv")):
        try:
            rows = read_csv(file_path)
        except Exception as exc:
            print(f"Skip {file_path.name}: {exc}")
            continue

        for index, row in enumerate(rows, start=1):
            rows_out.append(normalize_row(row, file_path.name, index))

    output_file = Path(args.output_file)
    output_file.parent.mkdir(parents=True, exist_ok=True)

    fieldnames = [
        "source_file",
        "source_row",
        "nama_alat",
        "kategori",
        "kode_barang",
        "jumlah_stok",
        "lokasi_simpan",
        "tahun_perolehan",
        "kondisi",
        "perlengkapan_detail",
        "is_borrowable",
    ]

    with output_file.open("w", encoding="utf-8", newline="") as handle:
        writer = csv.DictWriter(handle, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(rows_out)

    print(f"Selesai. Total baris: {len(rows_out)}")
    print(f"Output: {output_file}")


if __name__ == "__main__":
    main()
