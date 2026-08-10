<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migration Result</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { color: #333; border-bottom: 2px solid #007cba; padding-bottom: 10px; margin-bottom: 20px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .report { background: #f8f9fa; padding: 20px; border-radius: 5px; white-space: pre-wrap; font-family: monospace; font-size: 14px; line-height: 1.4; }
        .button { background: #007cba; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; text-decoration: none; display: inline-block; margin-top: 20px; }
        .button:hover { background: #005a8b; }
        .trace { background: #f1f1f1; padding: 15px; border-radius: 5px; margin-top: 10px; font-family: monospace; font-size: 12px; white-space: pre-wrap; max-height: 300px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="header">Migration Result</h1>
        
        <?php if ($success): ?>
            <div class="success">
                <strong>✓ Migration Completed Successfully!</strong>
                <p>WordPress directory data has been migrated to your CodeIgniter database.</p>
            </div>
            
            <h2>Migration Report</h2>
            <div class="report"><?= htmlspecialchars($report) ?></div>
            
        <?php else: ?>
            <div class="error">
                <strong>✗ Migration Failed</strong>
                <p><strong>Error:</strong> <?= htmlspecialchars($error) ?></p>
            </div>
            
            <?php if (isset($trace)): ?>
                <h3>Error Details</h3>
                <div class="trace"><?= htmlspecialchars($trace) ?></div>
            <?php endif; ?>
            
        <?php endif; ?>
        
        <a href="<?= base_url('migration') ?>" class="button">Back to Migration</a>
        <a href="<?= base_url() ?>" class="button">Go to Website</a>
    </div>
</body>
</html>