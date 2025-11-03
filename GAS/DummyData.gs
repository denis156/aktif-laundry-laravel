/**
 * DummyData.gs
 * Script untuk mengisi semua sheets dengan data dummy
 * Menggunakan format standardisasi:
 * - Harga: raw numbers (tanpa "Rp" atau formatting)
 * - Tanggal: format Y-m-d (YYYY-MM-DD)
 * - Angka: float/int murni
 */

/**
 * Fungsi untuk mengisi semua sheets dengan data dummy
 * Run fungsi ini setelah setupSheets() untuk testing
 */
function insertDummyData() {
  try {
    insertDummyUsers();
    insertDummyPelanggan();
    insertDummyLayanan();
    insertDummyJenisPakaian();
    insertDummyTransaksi();
    insertDummySetting();

    Logger.log('✅ Insert dummy data selesai - Semua data berhasil ditambahkan!');

    // Try to show alert if UI is available
    try {
      SpreadsheetApp.getUi().alert('Berhasil!', 'Data dummy berhasil ditambahkan.', SpreadsheetApp.getUi().ButtonSet.OK);
    } catch (uiError) {
      Logger.log('UI alert tidak tersedia, silakan lihat log untuk konfirmasi');
    }

  } catch (error) {
    Logger.log('❌ Error insert dummy data: ' + error);

    // Try to show error alert if UI is available
    try {
      SpreadsheetApp.getUi().alert('Error', 'Terjadi kesalahan: ' + error, SpreadsheetApp.getUi().ButtonSet.OK);
    } catch (uiError) {
      Logger.log('Silakan cek log untuk detail error');
    }
  }
}

/**
 * Insert dummy data Users
 * Password disimpan dalam plain text (untuk demo)
 * Di production sebaiknya di-hash
 */
function insertDummyUsers() {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName('Users');

  if (!sheet) {
    throw new Error('Sheet Users tidak ditemukan');
  }

  // Data users - password plain text
  var data = [
    ['admin', 'admin123'],
    ['kasir', 'kasir123'],
    ['demo', 'demo123']
  ];

  // Insert data mulai dari row 2
  sheet.getRange(2, 1, data.length, 2).setValues(data);

  Logger.log('✅ Dummy users inserted: ' + data.length + ' users');
}

/**
 * Insert dummy data Pelanggan
 * Format Tanggal Daftar: Y-m-d (2025-01-15)
 * Format Total Transaksi: int murni (5)
 */
function insertDummyPelanggan() {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName('Pelanggan');

  if (!sheet) {
    throw new Error('Sheet Pelanggan tidak ditemukan');
  }

  // Data menggunakan format Y-m-d dan angka murni
  var data = [
    ['PLG001', 'Budi Santoso', '81234567890', 'Jl. Merdeka No. 10, Jakarta', 'budi@email.com', '2025-01-15', '5', 'Aktif'],
    ['PLG002', 'Siti Nurhaliza', '81234567891', 'Jl. Sudirman No. 20, Jakarta', 'siti@email.com', '2025-02-10', '3', 'Aktif'],
    ['PLG003', 'Andi Wijaya', '81234567892', 'Jl. Thamrin No. 15, Jakarta', 'andi@email.com', '2025-03-05', '8', 'Aktif'],
    ['PLG004', 'Dewi Lestari', '81234567893', 'Jl. Gatot Subroto No. 25, Jakarta', 'dewi@email.com', '2025-04-12', '2', 'Aktif'],
    ['PLG005', 'Rudi Hermawan', '81234567894', 'Jl. Kuningan No. 30, Jakarta', 'rudi@email.com', '2025-05-20', '1', 'Tidak Aktif'],
    ['PLG006', 'Maya Sari', '81234567895', 'Jl. Casablanca No. 40, Jakarta', 'maya@email.com', '2025-06-18', '6', 'Aktif'],
    ['PLG007', 'Hendra Gunawan', '81234567896', 'Jl. Menteng No. 12, Jakarta', 'hendra@email.com', '2025-07-25', '4', 'Aktif'],
    ['PLG008', 'Rina Kartika', '81234567897', 'Jl. Senopati No. 8, Jakarta', 'rina@email.com', '2025-08-14', '7', 'Aktif'],
    ['PLG009', 'Agus Salim', '81234567898', 'Jl. Kebayoran No. 18, Jakarta', 'agus@email.com', '2025-09-10', '3', 'Aktif'],
    ['PLG010', 'Linda Permata', '81234567899', 'Jl. Blok M No. 22, Jakarta', 'linda@email.com', '2025-10-05', '5', 'Aktif']
  ];

  sheet.getRange(2, 1, data.length, data[0].length).setValues(data);
  Logger.log('Dummy data Pelanggan inserted');
}

/**
 * Insert dummy data Layanan
 * Format Harga per Kg: float/int murni (5000, bukan "Rp 5.000")
 * Format Durasi: int murni (24)
 */
function insertDummyLayanan() {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName('Layanan');

  if (!sheet) {
    throw new Error('Sheet Layanan tidak ditemukan');
  }

  // Data menggunakan angka murni tanpa formatting
  var data = [
    ['LYN001', 'Cuci Kering', '5000', '24', 'Cuci dan kering saja', 'Aktif'],
    ['LYN002', 'Cuci Setrika', '7000', '48', 'Cuci, kering, dan setrika rapi', 'Aktif'],
    ['LYN003', 'Setrika Saja', '4000', '12', 'Setrika saja tanpa cuci', 'Aktif'],
    ['LYN004', 'Express 6 Jam', '12000', '6', 'Layanan kilat 6 jam jadi', 'Aktif'],
    ['LYN005', 'Express 12 Jam', '10000', '12', 'Layanan kilat 12 jam jadi', 'Aktif'],
    ['LYN006', 'Cuci Karpet', '15000', '72', 'Cuci karpet dan permadani', 'Aktif'],
    ['LYN007', 'Cuci Sepatu', '25000', '48', 'Cuci sepatu sneakers', 'Aktif'],
    ['LYN008', 'Cuci Boneka', '20000', '24', 'Cuci boneka dan mainan', 'Tidak Aktif']
  ];

  sheet.getRange(2, 1, data.length, data[0].length).setValues(data);
  Logger.log('Dummy data Layanan inserted');
}

/**
 * Insert dummy data Jenis Pakaian
 */
function insertDummyJenisPakaian() {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName('JenisPakaian');

  if (!sheet) {
    throw new Error('Sheet JenisPakaian tidak ditemukan');
  }

  var data = [
    ['JNS001', 'Kemeja', 'Kemeja lengan panjang dan pendek', 'Aktif'],
    ['JNS002', 'Celana Panjang', 'Celana panjang reguler', 'Aktif'],
    ['JNS003', 'Celana Pendek', 'Celana pendek dan training', 'Aktif'],
    ['JNS004', 'Kaos', 'Kaos oblong dan t-shirt', 'Aktif'],
    ['JNS005', 'Rok', 'Rok panjang dan pendek', 'Aktif'],
    ['JNS006', 'Dress', 'Gaun dan terusan', 'Aktif'],
    ['JNS007', 'Jaket', 'Jaket dan hoodie', 'Aktif'],
    ['JNS008', 'Jas', 'Jas formal', 'Aktif'],
    ['JNS009', 'Kebaya', 'Kebaya dan pakaian adat', 'Aktif'],
    ['JNS010', 'Handuk', 'Handuk mandi', 'Aktif'],
    ['JNS011', 'Sprei', 'Sprei dan bed cover', 'Aktif'],
    ['JNS012', 'Selimut', 'Selimut dan blanket', 'Aktif'],
    ['JNS013', 'Sarung', 'Sarung dan sarung bantal', 'Aktif'],
    ['JNS014', 'Gordyn', 'Gorden jendela', 'Aktif'],
    ['JNS015', 'Mukena', 'Mukena dan perlengkapan sholat', 'Aktif']
  ];

  sheet.getRange(2, 1, data.length, data[0].length).setValues(data);
  Logger.log('Dummy data JenisPakaian inserted');
}

/**
 * Insert dummy data Transaksi
 * Format Tanggal Masuk & Tanggal Selesai: Y-m-d H:i (2025-10-20 08:00)
 * Format Berat: float murni (5, 3.5)
 * Format Harga/Subtotal/Diskon/Total: float murni (25000, bukan "Rp 25.000")
 * Format Jenis Pakaian: "Celana (2), Baju (10), Sarung (1)"
 */
function insertDummyTransaksi() {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName('Transaksi');

  if (!sheet) {
    throw new Error('Sheet Transaksi tidak ditemukan');
  }

  // Data menggunakan format Y-m-d H:i untuk datetime dan angka murni dalam string
  // Kolom Jenis Pakaian ditambahkan setelah Nama Layanan
  var data = [
    ['TRX001', '2025-10-20 08:00', 'PLG001', 'Budi Santoso', 'LYN001', 'Cuci Kering', 'Kemeja (3), Celana Panjang (2)', '5', '5000', '25000', '0', '25000', 'Tunai', '2025-10-21 10:00', 'Selesai', 'Pesanan regular'],
    ['TRX002', '2025-10-21 09:30', 'PLG002', 'Siti Nurhaliza', 'LYN002', 'Cuci Setrika', 'Dress (1), Rok (2)', '3', '7000', '21000', '2000', '19000', 'Transfer', '2025-10-23 14:00', 'Selesai', 'Member discount 10%'],
    ['TRX003', '2025-10-22 07:15', 'PLG003', 'Andi Wijaya', 'LYN004', 'Express 6 Jam', 'Jas (1), Kemeja (2), Celana Panjang (1)', '4', '12000', '48000', '0', '48000', 'QRIS', '2025-10-22 13:15', 'Diambil', 'Express service'],
    ['TRX004', '2025-10-23 10:45', 'PLG004', 'Dewi Lestari', 'LYN001', 'Cuci Kering', 'Kaos (5), Celana Pendek (2)', '7', '5000', '35000', '0', '35000', 'Tunai', '2025-10-24 12:00', 'Selesai', ''],
    ['TRX005', '2025-10-24 14:20', 'PLG006', 'Maya Sari', 'LYN003', 'Setrika Saja', 'Kemeja (4)', '2', '4000', '8000', '0', '8000', 'Debit', '2025-10-24 18:00', 'Diambil', 'Setrika rapi'],
    ['TRX006', '2025-10-25 11:00', 'PLG007', 'Hendra Gunawan', 'LYN002', 'Cuci Setrika', 'Kemeja (3), Celana Panjang (3)', '6', '7000', '42000', '4000', '38000', 'Transfer', '2025-10-27 16:00', 'Proses', 'Member discount'],
    ['TRX007', '2025-10-26 15:30', 'PLG008', 'Rina Kartika', 'LYN005', 'Express 12 Jam', 'Jaket (1), Kaos (2)', '3', '10000', '30000', '0', '30000', 'QRIS', '2025-10-27 03:30', 'Proses', 'Butuh cepat'],
    ['TRX008', '2025-10-27 08:45', 'PLG009', 'Agus Salim', 'LYN001', 'Cuci Kering', 'Kaos (6), Celana Pendek (2)', '4', '5000', '20000', '0', '20000', 'Tunai', '2025-10-28 10:00', 'Menunggu', ''],
    ['TRX009', '2025-10-28 13:00', 'PLG010', 'Linda Permata', 'LYN006', 'Cuci Karpet', 'Sprei (2), Selimut (1), Sarung (5)', '8', '15000', '120000', '10000', '110000', 'Transfer', '2025-10-31 17:00', 'Proses', 'Karpet besar 2x3m'],
    ['TRX010', '2025-10-29 09:15', 'PLG001', 'Budi Santoso', 'LYN002', 'Cuci Setrika', 'Kemeja (3), Celana Panjang (2)', '5', '7000', '35000', '0', '35000', 'Tunai', '2025-10-31 11:00', 'Menunggu', 'Pesanan kedua'],
    ['TRX011', '2025-10-30 10:00', 'PLG003', 'Andi Wijaya', 'LYN001', 'Cuci Kering', 'Handuk (10)', '3', '5000', '15000', '0', '15000', 'Tunai', '2025-10-31 12:00', 'Menunggu', 'Handuk hotel'],
    ['TRX012', '2025-10-30 14:30', 'PLG005', 'Rudi Hermawan', 'LYN007', 'Cuci Sepatu', 'Sepatu Sneakers (2)', '2', '25000', '50000', '0', '50000', 'Transfer', '2025-11-01 14:30', 'Menunggu', 'Sepatu putih'],
    ['TRX013', '2025-10-31 08:15', 'PLG008', 'Rina Kartika', 'LYN002', 'Cuci Setrika', 'Kebaya (1), Rok (1)', '2', '7000', '14000', '0', '14000', 'QRIS', '2025-11-02 10:00', 'Menunggu', 'Untuk acara'],
    ['TRX014', '2025-10-31 11:00', 'PLG002', 'Siti Nurhaliza', 'LYN001', 'Cuci Kering', 'Mukena (2)', '1', '5000', '5000', '0', '5000', 'Tunai', '2025-11-01 13:00', 'Menunggu', ''],
    ['TRX015', '2025-10-31 16:00', 'PLG006', 'Maya Sari', 'LYN003', 'Setrika Saja', 'Gordyn (1)', '3', '4000', '12000', '0', '12000', 'Debit', '2025-11-01 04:00', 'Menunggu', 'Gordyn ruang tamu']
  ];

  sheet.getRange(2, 1, data.length, data[0].length).setValues(data);
  Logger.log('Dummy data Transaksi inserted');
}

/**
 * Insert dummy data Setting
 * Setting tidak perlu format khusus, sudah sesuai
 */
function insertDummySetting() {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName('Setting');

  if (!sheet) {
    throw new Error('Sheet Setting tidak ditemukan');
  }

  var data = [
    ['nama_toko', 'Aktif Laundry', 'Nama toko laundry'],
    ['alamat', 'Kota Kendari, Kec. Lalolara', 'Alamat toko'],
    ['telepon', '021-12345678', 'Nomor telepon toko'],
    ['whatsapp', '81234567890', 'Nomor WhatsApp (format: 8xxx tanpa 0)'],
    ['email', 'info@aktiflaundry.com', 'Email toko'],
    ['format_id_transaksi', 'TRX', 'Prefix ID transaksi'],
    ['format_id_pelanggan', 'PLG', 'Prefix ID pelanggan'],
    ['format_id_layanan', 'LYN', 'Prefix ID layanan'],
    ['format_id_jenis_pakaian', 'JNS', 'Prefix ID jenis pakaian'],
    ['jam_buka', '08:00', 'Jam buka toko'],
    ['jam_tutup', '21:00', 'Jam tutup toko']
  ];

  sheet.getRange(2, 1, data.length, data[0].length).setValues(data);
  Logger.log('Dummy data Setting inserted');
}

/**
 * Fungsi untuk menghapus semua data (kecuali header)
 * Gunakan ini sebelum insertDummyData() untuk clean start
 */
function clearAllData() {
  try {
    clearSheetData('Pelanggan');
    clearSheetData('Layanan');
    clearSheetData('JenisPakaian');
    clearSheetData('Transaksi');
    clearSheetData('Setting');

    Logger.log('✅ Clear all data selesai - Semua data berhasil dihapus!');

    // Try to show alert if UI is available
    try {
      var ui = SpreadsheetApp.getUi();
      ui.alert('Berhasil!', 'Semua data berhasil dihapus.', ui.ButtonSet.OK);
    } catch (uiError) {
      Logger.log('UI alert tidak tersedia, silakan lihat log untuk konfirmasi');
    }

  } catch (error) {
    Logger.log('❌ Error clear data: ' + error);

    // Try to show error alert if UI is available
    try {
      var ui = SpreadsheetApp.getUi();
      ui.alert('Error', 'Terjadi kesalahan: ' + error, ui.ButtonSet.OK);
    } catch (uiError) {
      Logger.log('Silakan cek log untuk detail error');
    }
  }
}

/**
 * Fungsi dengan konfirmasi untuk menghapus semua data
 * Gunakan ini jika UI tersedia (run dari menu, bukan dari editor)
 */
function clearAllDataWithConfirm() {
  var ui = SpreadsheetApp.getUi();

  var response = ui.alert(
    'Konfirmasi Hapus Data',
    'Apakah Anda yakin ingin menghapus semua data? (Header tetap ada)',
    ui.ButtonSet.YES_NO
  );

  if (response == ui.Button.YES) {
    clearAllData();
  } else {
    Logger.log('Clear data dibatalkan oleh user');
  }
}

/**
 * Helper function - Clear data dari sheet tertentu
 */
function clearSheetData(sheetName) {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName(sheetName);

  if (!sheet) {
    throw new Error('Sheet ' + sheetName + ' tidak ditemukan');
  }

  var lastRow = sheet.getLastRow();

  if (lastRow > 1) {
    sheet.getRange(2, 1, lastRow - 1, sheet.getLastColumn()).clearContent();
  }

  Logger.log('Data sheet ' + sheetName + ' cleared');
}
