<?php

function caesarCipher($text, $shift, $decrypt = false) {
    $result = '';
    $shift = $decrypt ? 26 - $shift : $shift;
    
    for ($i = 0; $i < strlen($text); $i++) {
        $c = $text[$i];
        if (ctype_alpha($c)) {
            $offset = ctype_upper($c) ? 65 : 97;
            $result .= chr((ord($c) + $shift - $offset) % 26 + $offset);
        } else {
            $result .= $c;
        }
    }
    
    return $result;
}

function vigenereCipher($text, $key, $decrypt = false) {
    $key = strtoupper($key);
    $keyLength = strlen($key);
    $result = '';
    
    for ($i = 0, $j = 0; $i < strlen($text); $i++) {
        $c = strtoupper($text[$i]);
        
        if (ctype_alpha($c)) {
            $shift = ord($key[$j % $keyLength]) - 65;
            if ($decrypt) {
                $shift = 26 - $shift;
            }
            $result .= chr((ord($c) + $shift - 65) % 26 + 65);
            $j++;
        } else {
            $result .= $c;
        }
    }
    
    return $result;
}

function atbashCipher($text) {
    $result = '';
    
    for ($i = 0; $i < strlen($text); $i++) {
        $c = $text[$i];
        
        if (ctype_alpha($c)) {
            $offset = ctype_upper($c) ? 65 : 97;
            $result .= chr(25 - (ord($c) - $offset) + $offset);
        } else {
            $result .= $c;
        }
    }
    
    return $result;
}

function railFenceCipher($text, $key, $decrypt = false) {
    $result = '';
    $rails = array_fill(0, $key, []);
    $direction = false;
    $row = 0;

    for ($i = 0; $i < strlen($text); $i++) {
        $rails[$row][] = $text[$i];
        if ($row == 0 || $row == $key - 1) {
            $direction = !$direction;
        }
        $row += $direction ? 1 : -1;
    }

    if ($decrypt) {
        $indices = [];
        $idx = 0;
        for ($r = 0; $r < $key; $r++) {
            foreach ($rails[$r] as $c) {
                $indices[] = $idx++;
            }
        }
        sort($indices);
        $result = str_repeat(' ', strlen($text));
        for ($i = 0; $i < count($indices); $i++) {
            $result[$indices[$i]] = $text[$i];
        }
    } else {
        foreach ($rails as $rail) {
            $result .= implode('', $rail);
        }
    }

    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cipher = $_POST['cipher'];
    $key = $_POST['key'];
    $text = $_POST['text'];
    $action = $_POST['action'];
    $decrypt = $action === 'decrypt';
    
    switch ($cipher) {
        case 'caesar':
            $shift = (int)$key;
            echo caesarCipher($text, $shift, $decrypt);
            break;
        case 'vigenere':
            echo vigenereCipher($text, $key, $decrypt);
            break;
        case 'atbash':
            echo atbashCipher($text);
            break;
        case 'railfence':
            $rails = (int)$key;
            echo railFenceCipher($text, $rails, $decrypt);
            break;
        default:
            echo 'Nieznany szyfr';
            break;
    }
}
?>
