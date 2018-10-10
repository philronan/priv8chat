<?php

/* A simple wrapper for the Zxcvbn library */
if (!isset($ROOT)) $ROOT = '.';
require("$ROOT/zxcvbn/Matchers/MatchInterface.php");
require("$ROOT/zxcvbn/Matchers/Match.php");
require("$ROOT/zxcvbn/Matchers/DigitMatch.php");
require("$ROOT/zxcvbn/Matchers/Bruteforce.php");
require("$ROOT/zxcvbn/Matchers/YearMatch.php");
require("$ROOT/zxcvbn/Matchers/SpatialMatch.php");
require("$ROOT/zxcvbn/Matchers/SequenceMatch.php");
require("$ROOT/zxcvbn/Matchers/RepeatMatch.php");
require("$ROOT/zxcvbn/Matchers/DictionaryMatch.php");
require("$ROOT/zxcvbn/Matchers/L33tMatch.php");
require("$ROOT/zxcvbn/Matchers/DateMatch.php");
require("$ROOT/zxcvbn/Matcher.php");
require("$ROOT/zxcvbn/Searcher.php");
require("$ROOT/zxcvbn/ScorerInterface.php");
require("$ROOT/zxcvbn/Scorer.php");
require("$ROOT/zxcvbn/Zxcvbn.php");

use ZxcvbnPhp\Zxcvbn;

$zxcvbn = new Zxcvbn();

// is_strong_password()
// Include strings such as the user's name and email address in the
// $user_inputs array for additional checking. Returns TRUE if the
// password is acceptably strong.

function is_strong_password($password, $user_inputs=[]) {
    global $zxcvbn;
    return ($zxcvbn->passwordStrength($password, $user_inputs)['score'] > 2);
}
