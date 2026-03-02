# Vendor & Dispatch Operations Flow (MVP)

Dokumen ini adalah panduan praktis untuk Admin/Ops supaya alur booking → dispatch → settlement konsisten dengan validasi sistem saat ini.

## 1) Status yang dipakai sistem

### Booking
- `status`: `pending`, `confirmed`, `completed`, `cancelled`
- `payment_status`: `unpaid`, `paid`, `refunded`

### Dispatch Assignment
- `dispatch_status`: `pending`, `assigned`, `on_route`, `completed`, `cancelled`
- `settlement_status`: `unpaid`, `partially_paid`, `paid`

## 2) Rule wajib (enforced)

1. Booking hanya boleh `completed` jika `payment_status = paid`.
2. Dispatch hanya boleh `completed` jika booking `payment_status = paid`.
3. Settlement hanya boleh `paid` jika `dispatch_status = completed`.

## 3) Alur kerja harian (recommended)

1. **Pastikan booking sudah dibayar**
   - Cek `payment_status` di detail booking.
   - Jika masih `unpaid`, jangan lanjut ke completion.

2. **Assign vendor dan driver**
   - Isi vendor, nama driver, kontak, plat kendaraan.
   - Set awal `dispatch_status = assigned`.
   - Set awal `settlement_status = unpaid`.

3. **Monitoring perjalanan**
   - Saat driver berangkat, update jadi `on_route`.

4. **Selesaikan layanan**
   - Setelah layanan benar-benar selesai, update `dispatch_status = completed`.

5. **Settlement vendor**
   - Setelah transfer pembayaran vendor selesai, update `settlement_status = paid`.

6. **Close booking**
   - Update booking `status = completed`.

## 4) Matrix validasi cepat

| Kondisi | Diizinkan? | Catatan |
|---|---|---|
| `booking.status = completed` saat `payment_status = unpaid` | ❌ | Akan ditolak sistem |
| `dispatch_status = completed` saat booking `payment_status = unpaid` | ❌ | Akan ditolak sistem |
| `settlement_status = paid` saat `dispatch_status != completed` | ❌ | Akan ditolak sistem |
| `dispatch_status = assigned/on_route` saat booking `paid` | ✅ | Alur normal |
| `dispatch_status = completed` saat booking `paid` | ✅ | Alur normal |
| `settlement_status = paid` saat `dispatch_status = completed` | ✅ | Alur normal |

## 5) Rumus komisi

### Jika `commission_type = percentage`
- `commission_amount = total_price * commission_rate / 100`

### Jika `commission_type = fixed`
- `commission_amount = commission_flat_amount`

### Payout vendor
- `vendor_payout_amount = total_price - commission_amount`

## 6) Contoh skenario end-to-end (valid)

1. Booking A: `payment_status = paid`, `status = confirmed`.
2. Ops assign vendor + driver, `dispatch_status = assigned`, `settlement_status = unpaid`.
3. Ubah dispatch ke `on_route`.
4. Ubah dispatch ke `completed`.
5. Bayar vendor, ubah settlement ke `paid`.
6. Ubah booking ke `completed`.

## 7) Contoh yang pasti gagal

- Ops coba langsung set settlement ke `paid` saat dispatch masih `assigned` → ditolak.
- Ops coba set booking ke `completed` padahal belum dibayar (`unpaid`) → ditolak.
- Ops coba set dispatch `completed` padahal booking belum dibayar (`unpaid`) → ditolak.

## 8) Lokasi menu admin

- Vendor master: `Admin > Vendors`
- Dispatch monitor: `Admin > Dispatch`
- Assign dari booking: `Admin > Bookings > Detail > Operations`

---
Jika nanti mau diperketat lagi, rule yang sama bisa ditambahkan ke level database constraint agar aman juga dari query SQL langsung.
