<?php
$dir = "c:/xampp/htdocs/techscout.n/views/admin";
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === "php") {
        $content = file_get_contents($file->getPathname());
        
        // Find topbar button containers which are usually the first few divs with "display: flex; gap:"
        $pattern = "/<div[^>]*style=[\"'][^\"']*display:\s*flex;[^\"']*gap:\s*[^;]+;[^\"']*[\"'][^>]*>/i";
        $content = preg_replace_callback($pattern, function($matches) {
            $tag = $matches[0];
            if (!str_contains($tag, "flex-wrap")) {
                // Add flex-wrap: wrap and align-items: center right after style="
                $tag = preg_replace("/(style=[\"'])/i", "$1flex-wrap: wrap; align-items: center; ", $tag);
            }
            return $tag;
        }, $content, -1, $count);
        
        if ($count > 0) {
            file_put_contents($file->getPathname(), $content);
            echo "Fixed: " . $file->getFilename() . "\n";
        }
    }
}
?>
