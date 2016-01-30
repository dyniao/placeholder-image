<?php
if ($_SERVER['QUERY_STRING'] == "") {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Placeholder Image - dyniao.com</title>
        <style type="text/css">
        html, body {margin: 0;padding: 0;}
        body {background: #fff;}
        .header {width: 100%;}
        .content {width: 960px;margin: 0 auto;}
        .logo {width: 300px;height: 300px;margin: 0 auto;}
        </style>
    </head>
    <body>
    <div class="header"></div>
    <div class="content">
        <div class="logo"><img src="?/300x300/ae0/fff/Placeholder"></div>
        <div class="description">
			<p>http://tools.dyniao.com/100x100 (图片大小)</p>
			<p>http://tools.dyniao.com/100x100/fff (背景颜色)</p>
			<p>http://tools.dyniao.com/100x100/fff/000 (文本颜色)</p>
			<p>http://tools.dyniao.com/100x100/fff/000/text (待显示的文本)</p>
        </div>
    </div>
    </body>
    </html>
    <?php
} else {
    $ele = explode('/', $_SERVER['QUERY_STRING']);

    $size = isset($ele[1]) ? $ele[1] : "360x200";
    $bg = isset($ele[2]) ? $ele[2] : "34495e";
    $color = isset($ele[3]) ? $ele[3] : "fff";
    $text = isset($ele[4]) ? $ele[4] : $size;

    preg_match('/(\d{1,4})[xX]{1}(\d{1,4})/', $size, $mat);

    if ($mat) {
        $width = $mat[1];
        $height = $mat[2];
    } else {
        $width = 200;
        $height = 200;
        $text = $width . 'x' . $height;
    }

    if ($width < 1 || $height < 1) {
        die("too small!");
    }
    $area = $width * $height;
    if ($area >= 16000000 || $width > 9999 || $height > 9999) {
        die("to big!");
    }

    $newbg = color2rgb($bg);
    $newcolor = color2rgb($color);

    $font = 'wqy-microhei.ttc';
    $text_angle = 0;

    $fontsize = max(min($width / strlen($text) * 1.15, $height * 0.5), 5);

    $text = mb_convert_encoding(urldecode($text), "UTF-8", "auto");

    $rect = ImageTTFBBox($fontsize, 0, $font, $text);

    $a = deg2rad($text_angle);

    $ca = cos($a);
    $sa = sin($a);
    $ret = array();

    for ($i = 0; $i < 7; $i += 2) {
        $ret[$i] = round($rect[$i] * $ca + $rect[$i + 1] * $sa);
        $ret[$i + 1] = round($rect[$i + 1] * $ca - $rect[$i] * $sa);
    }

    $textWidth = ceil(($ret[4] - $ret[1]) * 1.07);
    $textHeight = ceil((abs($ret[7]) + abs($ret[1])) * 1);
    $textX = ceil(($width - $textWidth) / 2);
    $textY = ceil(($height - $textHeight) / 2 + $textHeight);

    header("Content-type:image/png");

    $im = imagecreatetruecolor($width, $height);
    $bg_color = imagecolorallocatealpha($im, $newbg[0], $newbg[1], $newbg[2], $newbg[3]);
    $text_color = imagecolorallocatealpha($im, $newcolor[0], $newcolor[1], $newcolor[2], $newcolor[3]);

    imageFilledRectangle($im, 0, 0, $width, $height, $bg_color);

    imagettftext($im, $fontsize, 0, $textX, $textY, $text_color, $font, $text);

    imagepng($im);
    imagedestroy($im);
}

function color2rgb($color)
{
    $rgba = '';
    if (strstr($color, ',')) {
        $c = explode(',', $color);
        $r = isset($c[0]) ? $c[0] : '0';
        $g = isset($c[1]) ? $c[1] : '0';
        $b = isset($c[2]) ? $c[2] : '0';
        $a = isset($c[3]) ? $c[3] : '0';
        $rgba = array($r, $g, $b, $a);

    } else {
        $c = str_replace("#", "", $color);

        if (strlen($c) == 3) {$r = hexdec(substr($c, 0, 1) . substr($c, 0, 1));$g = hexdec(substr($c, 1, 1) . substr($c, 1, 1));$b = hexdec(substr($c, 2, 1) . substr($c, 2, 1));
        } else {$r = hexdec(substr($c, 0, 2));$g = hexdec(substr($c, 2, 2));$b = hexdec(substr($c, 4, 2));
        }
        $rgba = array($r, $g, $b, 0);
    }
    return $rgba;
}
