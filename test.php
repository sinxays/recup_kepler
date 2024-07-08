<?php

include("fonctions.php");


$test_date = "2023/05/10";

$final_date = date('d/m/Y',strtotime($test_date));
var_dump($final_date);



$bdc = get_bdc_from_uuid("2d058652-38aa-48f1-8d5f-e480894ed7b1");


$time_pre = time();

$test_string = '{"code":401,"message":"Invalid authentication token"}';

$test_obj = json_decode($test_string);

var_dump($test_obj);

echo $test_obj->code;
echo gettype($test_obj->code);

sautdeligne();
sautdeligne();



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
  // si il trouve une erreur alors la suite ne s'execute pas
  echo additionner(4, 8);
} catch (Exception $e) // Nous allons attraper les exceptions "Exception" s'il y en a une qui est levée.
{
  echo 'Une exception a été lancée. Message d\'erreur : ', $e->getMessage(), '<br />';
  echo additionner(2, 2), '<br />';
}

sautdeligne();

echo 'Fin du script'; // Ce message s'affiche, ça prouve bien que le script est exécuté jusqu'au bout.



sautdeligne();

$test = recup_bdc(83259,'',1);

var_dump($test);


