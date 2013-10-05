<?php
// primeiro vamos realizar a leitura
// simples do INI, cada chave é transformada
// em um índice do array unidimensional
$ini = parse_ini_file('exemplo.ini');
print $ini['temp'] . '<br>';
print $ini['fonte'] . '<br>';

// agora iremos respeitar a hierarquia das
// seções do INI. O segundo parâmetro faz
// com que as seções sejam as chaves de acesso
// para este array multi-dimensional
$ini = parse_ini_file('exemplo.ini', true);
print $ini['paths']['temp'] . '<br>';
print $ini['layout']['fonte'] . '<br>';
?>