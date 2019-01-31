HTTPPort <?= $port; ?>

HTTPBindAddress 0.0.0.0
MaxHTTPConnections 2000
MaxClients 1000
MaxBandwidth 1000000
CustomLog "<?= $path_log; ?>ffservser.log"

<Stream status.html>
    Format status
</Stream>
