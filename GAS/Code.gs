/**
 * Code.gs - Entry Point untuk Google Apps Script
 * Aktif Laundry Management System
 *
 * File ini adalah entry point utama yang akan dijalankan pertama kali
 * Berisi menu custom dan fungsi-fungsi utama yang bisa dipanggil dari UI
 */

/**
 * Fungsi onOpen - Dijalankan otomatis saat spreadsheet dibuka
 * Membuat menu custom untuk memudahkan penggunaan
 */
function onOpen() {
  var ui = SpreadsheetApp.getUi();

  ui.createMenu('🧺 Aktif Laundry')
    .addSubMenu(ui.createMenu('⚙️ Setup')
      .addItem('Setup Sheets', 'runSetupSheets')
      .addSeparator()
      .addItem('Insert Dummy Data', 'runInsertDummyData')
      .addItem('Clear All Data', 'runClearAllData'))
    .addSeparator()
    .addItem('📊 Refresh Data', 'refreshData')
    .addItem('ℹ️ About', 'showAbout')
    .addToUi();

  Logger.log('Menu Aktif Laundry loaded');
}

/**
 * Run Setup Sheets dengan error handling untuk UI
 */
function runSetupSheets() {
  try {
    setupSheets();
  } catch (error) {
    Logger.log('Error in runSetupSheets: ' + error);
    SpreadsheetApp.getUi().alert('Error', 'Terjadi kesalahan saat setup sheets: ' + error, SpreadsheetApp.getUi().ButtonSet.OK);
  }
}

/**
 * Run Insert Dummy Data dengan konfirmasi
 */
function runInsertDummyData() {
  var ui = SpreadsheetApp.getUi();

  var response = ui.alert(
    'Insert Dummy Data',
    'Apakah Anda yakin ingin menambahkan data dummy?\n\nData yang akan ditambahkan:\n- 3 Users\n- 10 Pelanggan\n- 8 Layanan\n- 15 Jenis Pakaian\n- 15 Transaksi\n- Setting Toko',
    ui.ButtonSet.YES_NO
  );

  if (response == ui.Button.YES) {
    try {
      insertDummyData();
      ui.alert('Berhasil!', 'Data dummy berhasil ditambahkan.\n\nSilakan cek sheets:\n- Users (3)\n- Pelanggan (10)\n- Layanan (8)\n- JenisPakaian (15)\n- Transaksi (15)\n- Setting (11)', ui.ButtonSet.OK);
    } catch (error) {
      Logger.log('Error in runInsertDummyData: ' + error);
      ui.alert('Error', 'Terjadi kesalahan: ' + error, ui.ButtonSet.OK);
    }
  }
}

/**
 * Run Clear All Data dengan konfirmasi
 */
function runClearAllData() {
  var ui = SpreadsheetApp.getUi();

  var response = ui.alert(
    '⚠️ Konfirmasi Hapus Data',
    'Apakah Anda yakin ingin menghapus SEMUA data?\n\n❗ PERINGATAN:\n- Semua data Pelanggan akan dihapus\n- Semua data Layanan akan dihapus\n- Semua data Transaksi akan dihapus\n- Semua data Setting akan dihapus\n- Header tetap ada\n\nTindakan ini TIDAK BISA dibatalkan!',
    ui.ButtonSet.YES_NO
  );

  if (response == ui.Button.YES) {
    // Konfirmasi kedua
    var confirmResponse = ui.alert(
      '⚠️ Konfirmasi Terakhir',
      'Anda yakin 100% ingin menghapus semua data?\n\nKlik YES untuk melanjutkan, NO untuk batal.',
      ui.ButtonSet.YES_NO
    );

    if (confirmResponse == ui.Button.YES) {
      try {
        clearAllData();
        ui.alert('Berhasil!', 'Semua data berhasil dihapus.\n\nHeader tetap ada, Anda bisa mulai input data baru atau insert dummy data.', ui.ButtonSet.OK);
      } catch (error) {
        Logger.log('Error in runClearAllData: ' + error);
        ui.alert('Error', 'Terjadi kesalahan: ' + error, ui.ButtonSet.OK);
      }
    } else {
      ui.alert('Dibatalkan', 'Penghapusan data dibatalkan.', ui.ButtonSet.OK);
    }
  }
}

/**
 * Refresh Data - Force recalculation
 */
function refreshData() {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var ui = SpreadsheetApp.getUi();

  try {
    // Trigger recalculation dengan edit dummy cell
    var sheets = ['Pelanggan', 'Layanan', 'JenisPakaian', 'Transaksi', 'Setting'];

    for (var i = 0; i < sheets.length; i++) {
      var sheet = ss.getSheetByName(sheets[i]);
      if (sheet) {
        var lastRow = sheet.getLastRow();
        if (lastRow > 0) {
          // Touch the sheet to trigger recalc
          sheet.getRange(1, 1).activate();
        }
      }
    }

    ui.alert('Berhasil!', 'Data berhasil di-refresh.', ui.ButtonSet.OK);
    Logger.log('Data refreshed');

  } catch (error) {
    Logger.log('Error refresh data: ' + error);
    ui.alert('Error', 'Terjadi kesalahan: ' + error, ui.ButtonSet.OK);
  }
}

/**
 * Show About Dialog
 */
function showAbout() {
  var ui = SpreadsheetApp.getUi();

  var html = '<div style="font-family: Arial, sans-serif; padding: 10px;">' +
    '<h2 style="color: #4CAF50;">🧺 Aktif Laundry Management System</h2>' +
    '<p><strong>Version:</strong> 1.0.0</p>' +
    '<p><strong>Developer:</strong> Denis Djadian Ardika</p>' +
    '<hr>' +
    '<h3>Features:</h3>' +
    '<ul>' +
    '<li>✅ Manajemen Pelanggan</li>' +
    '<li>✅ Manajemen Layanan</li>' +
    '<li>✅ Manajemen Jenis Pakaian</li>' +
    '<li>✅ Manajemen Transaksi</li>' +
    '<li>✅ Pengaturan Toko</li>' +
    '<li>✅ Auto ID Generation</li>' +
    '<li>✅ Data Validation</li>' +
    '</ul>' +
    '<hr>' +
    '<h3>Data Format:</h3>' +
    '<ul>' +
    '<li>Tanggal: Y-m-d (2025-01-15)</li>' +
    '<li>Harga: Raw numbers (7000)</li>' +
    '<li>Status: Dropdown validation</li>' +
    '</ul>' +
    '<hr>' +
    '<p style="text-align: center; color: #666; font-size: 12px;">© 2025 Aktif Laundry. All rights reserved.</p>' +
    '</div>';

  var htmlOutput = HtmlService.createHtmlOutput(html)
    .setWidth(400)
    .setHeight(500);

  ui.showModalDialog(htmlOutput, 'About Aktif Laundry');
}

/**
 * Custom function untuk format currency (bisa digunakan di formula)
 * Usage: =FORMATRUPIAH(A1)
 */
function FORMATRUPIAH(number) {
  if (typeof number !== 'number') {
    return 'Invalid';
  }

  return 'Rp ' + number.toLocaleString('id-ID');
}

/**
 * Custom function untuk format tanggal Indonesia
 * Usage: =FORMATTANGGAL(A1)
 */
function FORMATTANGGAL(dateString) {
  if (!dateString) return '';

  try {
    var date = new Date(dateString);
    var options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
  } catch (error) {
    return dateString;
  }
}
