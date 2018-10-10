<?php

require("zxcvbn/Matchers/MatchInterface.php");
require("zxcvbn/Matchers/Match.php");
require("zxcvbn/Matchers/DigitMatch.php");
require("zxcvbn/Matchers/Bruteforce.php");
require("zxcvbn/Matchers/YearMatch.php");
require("zxcvbn/Matchers/SpatialMatch.php");
require("zxcvbn/Matchers/SequenceMatch.php");
require("zxcvbn/Matchers/RepeatMatch.php");
require("zxcvbn/Matchers/DictionaryMatch.php");
require("zxcvbn/Matchers/L33tMatch.php");
require("zxcvbn/Matchers/DateMatch.php");
require("zxcvbn/Matcher.php");
require("zxcvbn/Searcher.php");
require("zxcvbn/ScorerInterface.php");
require("zxcvbn/Scorer.php");
require("zxcvbn/Zxcvbn.php");

use ZxcvbnPhp\Zxcvbn;

$userData = array(
  'justmyl.uk',
  'marco@justmyl.uk',
  'blackbettybamalam',
  'malamabyttebkcalb'
);

$zxcvbn = new Zxcvbn();
$strength = $zxcvbn->passwordStrength('esrohtcerroc');
echo $strength['score'];
$strength = $zxcvbn->passwordStrength('correcthorse');
echo $strength['score'];
$strength = $zxcvbn->passwordStrength('correct horse battery staple');
echo $strength['score'];
$strength = $zxcvbn->passwordStrength('justmyl.uk', $userData);
echo $strength['score'];
$strength = $zxcvbn->passwordStrength('marco@justmyl.uk');
echo $strength['score'];
$strength = $zxcvbn->passwordStrength('marco@justmyl.uk', $userData);
echo $strength['score'];
$strength = $zxcvbn->passwordStrength('ku.lymtsuj@ocram');
echo $strength['score'];
$strength = $zxcvbn->passwordStrength('ku.lymtsuj@ocram', $userData);
echo $strength['score'];
