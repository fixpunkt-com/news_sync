# TYPO3 Extension `news_sync`

This Extension is very unpolished and you **DEFINITLY** want to backup your db and fileadmin before using it!

Please think about contributing to this project, if you have the time or need it for your projects or work anyway.

Move news records including relations from one site to another

## Manual

Create a new database on target DB, e.g. with name `sync`. Copy all relevant database tables from source DB to this DB and register connection in AdditionalConfiguration

There is a little helper script which will take your db and filter out all the tables that are needed and prefix them with **sync_**.

You need to set the ENV Variables: **database** and **output** with the sql filenames of the input db and where the output should be saved.

If you cannot create / use a second database, you can just use your actual database, just use a development enviroment and or a backup!

```
$importKey = 'sync';
$importTables = [
    'sys_category',
    'sys_category_record_mm',
    'sys_file',
    'sys_file_metadata',
    'sys_file_reference',
    'tt_content',
    'tx_news_domain_model_news',
    'tx_news_domain_model_news_related_mm',
    'tx_news_domain_model_news_tag_mm',
    'tx_news_domain_model_news_ttcontent_mm',
    'tx_news_domain_model_tag',
    'tx_news_domain_model_link',
];

$customChanges['DB']['Connections'][$importKey] = [
    'dbname' => $importKey,
    'driver' => 'mysqli',
    'host' => 'mysql',
    'password' => getenv('MYSQL_PASSWORD'),
    'port' => 3306,
    'user' => 'root',
];

foreach ($importTables as $importTable) {
    $tbl = $importKey . '_' . $importTable;

    $customChanges['DB']['TableMapping'][$tbl] = $importKey;
}

$GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive($GLOBALS['TYPO3_CONF_VARS'], $customChanges);
```

**All tables must have a prefix `sync_`**!

**Remove column `content_elements` in tx_news_domain_model_news` of source DB.**

## Usage

- Do migration: `./typo3cms news:sync`
- Sync all images: `./typo3cms news:imagesync`
