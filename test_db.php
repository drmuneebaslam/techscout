<?php require 'app/Core/Config.php'; require 'app/Core/Database.php'; print_r(\App\Core\Database::query('SELECT template_key FROM mail_templates')); ?>
