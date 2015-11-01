<?php

/*
|-------------------------------------------------------------------------------
| Configure Your SQL Server Database Connection
|
| Remember that loading the script will hang for a 
| few seconds if the credentials are incorrect.
|-------------------------------------------------------------------------------
*/

$database = array(
    'server' => 'VM2012\SQLEXPRESS',
    'database' => 'peon',
    'username' => 'sa',
    'password' => 'Secret#01',
);

/*
|-------------------------------------------------------------------------------
| End Of User Configuration Area
|-------------------------------------------------------------------------------
*/

$rewrite = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="Imported Rule 1" stopProcessing="true">
                    <match url="^(.*)/$" ignoreCase="false" />
                    <conditions logicalGrouping="MatchAll">
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                    </conditions>
                    <action type="Redirect" url="/{R:1}" redirectType="Permanent" />
                </rule>
                <rule name="Imported Rule 2" stopProcessing="true">
                    <match url="^" ignoreCase="false" />
                    <conditions logicalGrouping="MatchAll">
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php" />
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
EOT;
?>
<!DOCTYPE html>
<html>
<head>
    <title>IIS Checklist - Winland</title>
    <style type="text/css">
        * { font-family: sans-serif; }
        body { margin: 0; overflow-x: hidden; }
        h2 { padding: 12px; border-left: solid 7px #aaa; }
        h4>small { font-size: 14px; font-weight: normal; }
        header { background-color: #44b; min-height: 50px; border-bottom: solid 1px #008; box-shadow: 0px 0px 10px black;}
        header>div.brand { margin-left: 20px; font-size: 30px; font-weight: bold; line-height: 1.5; color: #ddd; text-shadow: 2px 2px #008; }
        a { color: inherit; }
        table { border-collapse: collapse; }
        tr { border-bottom: solid 1px silver; }
        th { text-align: left; white-space: nowrap; }
        th, td { padding: 10px; }
        td { border-left: solid 1px silver; }
        td.result { text-align: center; font-size: larger; font-weight: bold; }
        .result-positive { color: #0a0; }
        .result-negative { color: #a00; }
        pre { padding: 10px; background-color: #eee; }
        code { font-family: monospace; font-size: 15px; color: #007;}
        div.content { margin: 60px; }
    </style>
</head>
<body>

<header>
    <div class="brand">Winland</div>
</header>

<div class="content">

    <h1>IIS Checklist</h1>
    <table>
        <tr>
            <th>PDO</th>
<?php
try {
    $pdo = new PDO("sqlsrv:server={$database['server']};database={$database['database']}", $database['username'], $database['password']);
    echo '<td class="result result-positive">&#10004;</td>';
    echo '<td class="result-positive">PDO connected to the server successfully.</td>';
} catch (Exception $e) {
    echo '<td class="result result-negative">&times;</td>';
    echo '<td class="result-negative">PDO could NOT connect to the server. Make sure you have properly assigned your credentials to the <code>$database</code> array at the top of this script.</td>';
}
?>
        </tr>
        <tr>
            <th>URL Rewrite Loaded</th>
            <?php if (isset($_SERVER['IIS_UrlRewriteModule']) && ( PHP_SAPI == 'cgi-fcgi' )): ?>
                <td class="result result-positive">&#10004;</td>
                <td class="result-positive">The URL rewrite module is loaded.</td>
            <?php else: ?>
                <td class="result result-negative">&#x2717;</td>
                <td class="result-negative">The URL rewrite module is NOT loaded.</td>
            <?php endif ?>
        </tr>
        <tr>
            <th>URL Rewrite Rule</th>
            <?php if ($_SERVER['REQUEST_URI'] == '/'): ?>
                <td class="result result-negative">&#x2717;</td>
                <td class="result-negative">
                    <p>Can't check the rewrite rule from the root URL.</p>
                    <p>
                        If you get error "<strong>HTTP Error 404.0 - Not Found</strong>" when you click <a href="/page/welcome">this</a> link,<br>
                        copy the code below into file <code>web.config</code>, place it in the document root and try again*.
                    </p>
                        <small>
                            * - If there is already a <code>web.config</code> file in the document root,
                            make sure you make a backup copy such as <code>web.config.bak</code>.
                        </small>
                        <pre><h5>web.config</h5><code><?php echo htmlentities($rewrite) ?></code></pre>
                </td>
            <?php else: ?>
                <?php if ($_SERVER['SCRIPT_NAME'] == '/index.php'): ?>
                    <td class="result result-positive">&#10004;</td>
                    <td class="result-positive">The rewrite rule is functioning correctly.</td>
                <?php else: ?>
                    <td class="result result-negative">&#x2717;</td>
                    <td class="result-positive">The rewrite rule is NOT functioning correctly.</td>
                <?php endif ?>
            <?php endif ?>
        </tr>
        <tr>
            <th>Mcrypt</th>
            <?php if(function_exists("mcrypt_encrypt")): ?>
                <td class="result result-positive">&#10004;</td>
                <td class="result-positive">Mcrypt is loaded</td>
            <?php else: ?>
                <td class="result result-positive">&#x2717;</td>
                <td class="result-positive">Mcrypt is NOT loaded</td>
            <?php endif ?>
        </tr>
    </table>

</div>

</body>
</html>
