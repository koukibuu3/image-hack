<?php
    require_once("lib/simple_html_dom.php");

    // URLを取得
    echo "url: ";
    $url = trim(fgets(STDIN));
    // 指定したURLが存在しないor不正な入力の場合は終了する
    if (!filter_var($url, FILTER_VALIDATE_URL) || !file_get_contents($url)) {
        echo "URLの指定が不正です。\n";
        exit;
    }

    // ページ数を取得
    echo "page: ";
    $pageValue = trim(fgets(STDIN));;

    // 変数定義
    $dirName = "images"; // 書き出し先ディレクトリは固定で "images" に設定
    $fileName = "image"; // ファイル名は固定で "image" に設定
    $path = $dirName . "/" . $fileName;

    // ディレクトリが存在しない場合は作成する
    if (!file_exists($dirName)) {
        mkdir($dirName);
    }

    // ページ数分繰り返す
    $suffix = 0;
    for ($i = 1; $i <= $pageValue; $i++) {
        // URLを設定
        $pageUrl = $url;
        if ($i !== 1) {
            $pageUrl = $url . "/" . $i . "/";
        }
        // 処理開始ページのURLをログ出力する
        echo $i . ": " . $pageUrl . "\n";
        // 対象ページのhtmlを取得
        $html = file_get_html($pageUrl);

        // ダウンロード対象のurlを抽出
        foreach ($html->find("img") as $element) {
            if (preg_match("/alignnone/", $element->class)) {
                $suffix += 1;
                // ローカルの保存先パスを作成
                $path = $dirName . "/" . $fileName . "-" . $suffix . ".jpg";
                // 画像ファイルを取得
                $data = file_get_contents($element->src);
                // 1ページごとにダウンロードのログ出力
                echo "[ Downloading... ]\n" . $element->src . " --> " . $path . "\n";
                // 保存処理
                file_put_contents($path, $data);
            }
        }
        // 1ページ処理完了ごとにログ出力する
        echo $i . " page end. ( " . $i . " of " . $pageValue . " )\n" ;
    }
?>