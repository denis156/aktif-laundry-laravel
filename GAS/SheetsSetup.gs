/**
 * Setup awal sheets untuk sistem kasir Aktif Laundry
 * Run fungsi ini sekali saat pertama kali setup
 */
function setupSheets() {
  var ss = SpreadsheetApp.getActiveSpreadsheet();

  try {
    // Hapus sheet default jika ada
    var defaultSheet = ss.getSheetByName('Sheet1');
    if (defaultSheet && ss.getSheets().length > 1) {
      ss.deleteSheet(defaultSheet);
    }

    // Buat semua sheets yang dibutuhkan
    createUsersSheet(ss);
    createPelangganSheet(ss);
    createLayananSheet(ss);
    createJenisPakaianSheet(ss);
    createTransaksiSheet(ss);
    createSettingSheet(ss);

    Logger.log('✅ Setup sheets selesai - Semua sheets berhasil dibuat!');

    // Try to show alert if UI is available
    try {
      SpreadsheetApp.getUi().alert('Setup berhasil!', 'Semua sheets sudah dibuat.', SpreadsheetApp.getUi().ButtonSet.OK);
    } catch (uiError) {
      Logger.log('UI alert tidak tersedia, silakan lihat log untuk konfirmasi');
    }

  } catch (error) {
    Logger.log('❌ Error setup: ' + error);

    // Try to show error alert if UI is available
    try {
      SpreadsheetApp.getUi().alert('Error', 'Terjadi kesalahan: ' + error, SpreadsheetApp.getUi().ButtonSet.OK);
    } catch (uiError) {
      Logger.log('Silakan cek log untuk detail error');
    }
  }
}

/**
 * Sheet Users
 */
function createUsersSheet(ss) {
  var sheetName = 'Users';
  var sheet = ss.getSheetByName(sheetName);

  // Hapus jika sudah ada
  if (sheet) {
    ss.deleteSheet(sheet);
  }

  // Buat sheet baru
  sheet = ss.insertSheet(sheetName);

  // Header
  var headers = [
    'Username',
    'Password'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers]);

  // Format header
  sheet.getRange(1, 1, 1, headers.length)
    .setBackground('#9C27B0')
    .setFontColor('#FFFFFF')
    .setFontWeight('bold')
    .setHorizontalAlignment('center');

  // Auto resize columns
  for (var i = 1; i <= headers.length; i++) {
    sheet.autoResizeColumn(i);
  }

  // Freeze header row
  sheet.setFrozenRows(1);

  // Protect sheet untuk keamanan
  var protection = sheet.protect().setDescription('Users Sheet - Protected');
  protection.setWarningOnly(true);

  Logger.log('Sheet Users created');
}

/**
 * Sheet Pelanggan
 */
function createPelangganSheet(ss) {
  var sheetName = 'Pelanggan';
  var sheet = ss.getSheetByName(sheetName);

  // Hapus jika sudah ada
  if (sheet) {
    ss.deleteSheet(sheet);
  }

  // Buat sheet baru
  sheet = ss.insertSheet(sheetName);

  // Header
  var headers = [
    'ID Pelanggan',
    'Nama',
    'No. HP',
    'Alamat',
    'Email',
    'Tanggal Daftar',
    'Total Transaksi',
    'Status'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers]);

  // Format header
  sheet.getRange(1, 1, 1, headers.length)
    .setBackground('#4CAF50')
    .setFontColor('#FFFFFF')
    .setFontWeight('bold')
    .setHorizontalAlignment('center');

  // Auto resize columns
  for (var i = 1; i <= headers.length; i++) {
    sheet.autoResizeColumn(i);
  }

  // Freeze header row
  sheet.setFrozenRows(1);

  // Data validation untuk Status
  var statusRange = sheet.getRange(2, 8, 1000, 1);
  var statusRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Aktif', 'Tidak Aktif'])
    .setAllowInvalid(false)
    .build();
  statusRange.setDataValidation(statusRule);

  Logger.log('Sheet Pelanggan created');
}

/**
 * Sheet Layanan
 */
function createLayananSheet(ss) {
  var sheetName = 'Layanan';
  var sheet = ss.getSheetByName(sheetName);

  if (sheet) {
    ss.deleteSheet(sheet);
  }

  sheet = ss.insertSheet(sheetName);

  var headers = [
    'ID Layanan',
    'Nama Layanan',
    'Harga per Kg',
    'Durasi (Jam)',
    'Deskripsi',
    'Status'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers]);

  sheet.getRange(1, 1, 1, headers.length)
    .setBackground('#2196F3')
    .setFontColor('#FFFFFF')
    .setFontWeight('bold')
    .setHorizontalAlignment('center');

  for (var i = 1; i <= headers.length; i++) {
    sheet.autoResizeColumn(i);
  }

  sheet.setFrozenRows(1);

  // Data validation untuk Status
  var statusRange = sheet.getRange(2, 6, 1000, 1);
  var statusRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Aktif', 'Tidak Aktif'])
    .setAllowInvalid(false)
    .build();
  statusRange.setDataValidation(statusRule);

  // Format number untuk harga (raw number, display dengan format)
  sheet.getRange(2, 3, 1000, 1).setNumberFormat('#,##0');

  Logger.log('Sheet Layanan created');
}

/**
 * Sheet Jenis Pakaian
 */
function createJenisPakaianSheet(ss) {
  var sheetName = 'JenisPakaian';
  var sheet = ss.getSheetByName(sheetName);

  if (sheet) {
    ss.deleteSheet(sheet);
  }

  sheet = ss.insertSheet(sheetName);

  var headers = [
    'ID Jenis',
    'Nama Jenis',
    'Keterangan',
    'Status'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers]);

  sheet.getRange(1, 1, 1, headers.length)
    .setBackground('#9C27B0')
    .setFontColor('#FFFFFF')
    .setFontWeight('bold')
    .setHorizontalAlignment('center');

  for (var i = 1; i <= headers.length; i++) {
    sheet.autoResizeColumn(i);
  }

  sheet.setFrozenRows(1);

  // Data validation untuk Status
  var statusRange = sheet.getRange(2, 4, 1000, 1);
  var statusRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Aktif', 'Tidak Aktif'])
    .setAllowInvalid(false)
    .build();
  statusRange.setDataValidation(statusRule);

  Logger.log('Sheet JenisPakaian created');
}

/**
 * Sheet Transaksi
 */
function createTransaksiSheet(ss) {
  var sheetName = 'Transaksi';
  var sheet = ss.getSheetByName(sheetName);

  if (sheet) {
    ss.deleteSheet(sheet);
  }

  sheet = ss.insertSheet(sheetName);

  var headers = [
    'ID Transaksi',
    'Tanggal Masuk',
    'ID Pelanggan',
    'Nama Pelanggan',
    'ID Layanan',
    'Nama Layanan',
    'Jenis Pakaian',
    'Berat (Kg)',
    'Harga per Kg',
    'Subtotal',
    'Diskon',
    'Total',
    'Metode Pembayaran',
    'Tanggal Selesai',
    'Status',
    'Catatan'
  ];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers]);

  sheet.getRange(1, 1, 1, headers.length)
    .setBackground('#FF9800')
    .setFontColor('#FFFFFF')
    .setFontWeight('bold')
    .setHorizontalAlignment('center');

  for (var i = 1; i <= headers.length; i++) {
    sheet.autoResizeColumn(i);
  }

  sheet.setFrozenRows(1);

  // Data validation untuk Metode Pembayaran
  var metodeRange = sheet.getRange(2, 13, 1000, 1);
  var metodeRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Tunai', 'Transfer', 'QRIS', 'Debit'])
    .setAllowInvalid(false)
    .build();
  metodeRange.setDataValidation(metodeRule);

  // Data validation untuk Status
  var statusRange = sheet.getRange(2, 15, 1000, 1);
  var statusRule = SpreadsheetApp.newDataValidation()
    .requireValueInList(['Menunggu', 'Proses', 'Selesai', 'Diambil', 'Batal'])
    .setAllowInvalid(false)
    .build();
  statusRange.setDataValidation(statusRule);

  // Format number untuk kolom harga (raw number, display dengan format)
  sheet.getRange(2, 9, 1000, 1).setNumberFormat('#,##0');
  sheet.getRange(2, 10, 1000, 1).setNumberFormat('#,##0');
  sheet.getRange(2, 11, 1000, 1).setNumberFormat('#,##0');
  sheet.getRange(2, 12, 1000, 1).setNumberFormat('#,##0');

  Logger.log('Sheet Transaksi created');
}

/**
 * Sheet Setting
 */
function createSettingSheet(ss) {
  var sheetName = 'Setting';
  var sheet = ss.getSheetByName(sheetName);

  if (sheet) {
    ss.deleteSheet(sheet);
  }

  sheet = ss.insertSheet(sheetName);

  var headers = ['Key', 'Value', 'Deskripsi'];

  sheet.getRange(1, 1, 1, headers.length).setValues([headers]);

  sheet.getRange(1, 1, 1, headers.length)
    .setBackground('#607D8B')
    .setFontColor('#FFFFFF')
    .setFontWeight('bold')
    .setHorizontalAlignment('center');

  for (var i = 1; i <= headers.length; i++) {
    sheet.autoResizeColumn(i);
  }

  sheet.setFrozenRows(1);

  // Protect sheet (opsional)
  var protection = sheet.protect().setDescription('Setting Sheet');
  protection.setWarningOnly(true);

  Logger.log('Sheet Setting created');
}

/**
 * Helper function - Get setting value
 */
function getSetting(key) {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName('Setting');

  if (!sheet) return null;

  var data = sheet.getDataRange().getValues();

  for (var i = 1; i < data.length; i++) {
    if (data[i][0] == key) {
      return data[i][1];
    }
  }

  return null;
}

/**
 * Helper function - Update setting value
 */
function updateSetting(key, value) {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName('Setting');

  if (!sheet) return false;

  var data = sheet.getDataRange().getValues();

  for (var i = 1; i < data.length; i++) {
    if (data[i][0] == key) {
      sheet.getRange(i + 1, 2).setValue(value);
      return true;
    }
  }

  return false;
}
