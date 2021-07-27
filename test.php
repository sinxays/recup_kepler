<?php

include("fonctions.php");

$time_pre = time();


function additionner($a, $b)
{
  if (!is_numeric($a) || !is_numeric($b)) {
    throw new Exception('Les deux paramètres doivent être des nombres');
  }

  return $a + $b;
}

try // Nous allons essayer d'effectuer les instructions situées dans ce bloc.
{
  echo additionner(12, 3), '<br />';
  echo additionner('azerty', 54), '<br />';
  echo additionner(4, 8);
} catch (Exception $e) // Nous allons attraper les exceptions "Exception" s'il y en a une qui est levée.
{
  echo 'Une exception a été lancée. Message d\'erreur : ', $e->getMessage(), '<br />';
  echo additionner(2, 2), '<br />';
}

sautdeligne();

echo 'Fin du script'; // Ce message s'affiche, ça prouve bien que le script est exécuté jusqu'au bout.



sautdeligne();


$test = "Factur\u00e9 \u00e9dit\u00e9";


// recup valeur token seulement
for ($i = 0; $i < 10; $i++) {
  $url = "https://www.kepler-soft.net/api/v3.0/auth-token/";
  $valeur_token = goCurlToken($url);
  echo $valeur_token. ' <br/>';
}

$time_post = time();

$exec_time = $time_post - $time_pre;

echo $exec_time.' <br/><br/>';


$time_post = time(); 

echo $time_post.'<br/>';

print_r(Utf8_ansi($test));
