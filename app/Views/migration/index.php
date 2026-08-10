<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress Migration</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { color: #333; border-bottom: 2px solid #007cba; padding-bottom: 10px; margin-bottom: 20px; }
        .check { display: flex; align-items: center; margin: 10px 0; }
        .check.pass { color: #28a745; }
        .check.fail { color: #dc3545; }
        .status { font-weight: bold; margin-right: 10px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-box { background: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; color: #007cba; }
        .stat-label { color: #666; font-size: 14px; }
        .button { background: #007cba; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; }
        .button:hover { background: #005a8b; }
        .button:disabled { background: #ccc; cursor: not-allowed; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="header">WordPress Directory Migration</h1>
        
        <div class="warning">
            <strong>Important:</strong> This is a backend-only migration. No frontend components will be modified.
        </div>
        
        <h2>System Verification</h2>
        
        <div class="check <?= $checks['sql_file'] ? 'pass' : 'fail' ?>">
            <span class="status"><?= $checks['sql_file'] ? '✓' : '✗' ?></span>
            <span>WordPress SQL file found</span>
        </div>
        
        <div class="check <?= $checks['uploads_folder'] ? 'pass' : 'fail' ?>">
            <span class="status"><?= $checks['uploads_folder'] ? '✓' : '✗' ?></span>
            <span>WordPress uploads folder accessible</span>
        </div>
        
        <div class="check <?= $checks['target_folder'] ? 'pass' : 'fail' ?>">
            <span class="status"><?= $checks['target_folder'] ? '✓' : '✗' ?></span>
            <span>Target uploads folder writable</span>
        </div>
        
        <h2>Current Database Statistics</h2>
        
        <div class="stats">
            <div class="stat-box">
                <div class="stat-number"><?= $stats['businesses'] ?></div>
                <div class="stat-label">Total Businesses</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $stats['categories'] ?></div>
                <div class="stat-label">Categories</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $stats['businesses_with_images'] ?></div>
                <div class="stat-label">With Images</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $stats['businesses_english'] ?></div>
                <div class="stat-label">English Names</div>
            </div>
            <div class="stat-box">
                <div class="stat-number"><?= $stats['businesses_urdu'] ?></div>
                <div class="stat-label">Urdu Names</div>
            </div>
        </div>
        
        <?php if (array_filter($checks)): ?>
            <div class="info">
                <strong>Ready to Migrate:</strong> All system checks passed. You can proceed with the migration.
            </div>
            
            <a href="<?= base_url('migration/execute') ?>" class="button">Execute Migration</a>
        <?php else: ?>
            <div class="warning">
                <strong>Cannot Migrate:</strong> Please fix the failed system checks before proceeding.
            </div>
            
            <button class="button" disabled>Execute Migration</button>
        <?php endif; ?>
        
        <div class="info" style="margin-top: 20px;">
            <strong>Migration Process:</strong>
            <ul>
                <li>Parse WordPress geodirectory data from SQL dump</li>
                <li>Match existing businesses to avoid duplicates</li>
                <li>Merge missing data into existing businesses</li>
                <li>Create new businesses from WordPress data</li>
                <li>Copy and link business images</li>
                <li>Preserve all bilingual content (English/Urdu)</li>
                <li>Generate comprehensive migration report</li>
            </ul>
        </div>
    </div>
</body>
</html>