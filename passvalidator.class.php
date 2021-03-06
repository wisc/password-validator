<?php

$dataConnection = mysqli_connect('whoopwhoop', 'whoop', 'whoooooooooooop', 'whoop?');


class PassValidator {
	
	public $connection;				// FUUUUUUUUUUUU
	
	
	public function feedDB ( $dataConnection ) {
		
		$this->connection = $dataConnection;
	}
	
	public function verifyAwesomeness ( $string, $validateRepetition = true, $validateForActualWords = true, $validateForLeetSpeak = false ) {
		
		$score = 0;
		$messages = array();
		$messages['serious'] = array();
		$messages['optional'] = array();
		
		if ( strlen($string) == 0 ) {
			/*
			$score = 1337;
			$messages['optional'][] = 'You, dear sir, are a genius.';
			
			return array($score, $messages);*/
		}
		elseif ( strlen( $string) <= 6 ) {
			$score += 1;
			$messages['serious'][] = 'Wachtwoord is wat kort';
		}
		elseif ( strlen( $string) <= 10 ) {
			$score += 3;
			$messages['optional'][] = 'Wachtwoordlengte is prima. Maar kan langer. Op zich.';
		}
		elseif ( strlen( $string) <= 15 )
			$score += 4;
		elseif ( strlen( $string) <= 20 )
			$score += 6;
		elseif ( strlen( $string) > 20 )
			$score += 7;
		
		preg_match_all('([a-z]{1})', $string, $lowercase);
		$lowercaseOccurrences = count($lowercase[0]);
		
		
		preg_match_all('([A-Z]{1})', $string, $uppercase);
		$uppercaseOccurrences = count($uppercase[0]);
		
		
		preg_match_all('([0-9]{1})', $string, $numbers);
		$numberOccurrences = count($numbers[0]);
		
		$specialOccurrences = strlen($string) - $lowercaseOccurrences - $uppercaseOccurrences - $numberOccurrences;
		

		if ( $lowercaseOccurrences == 0 || $uppercaseOccurrences == 0 || $numberOccurrences == 0 ) {
			$score--;
			$messages['serious']['mentioned_numeriek'] = 'It helps when you use lowercase, uppercase and numbers.';
		}
		
		if ( $lowercaseOccurrences == 0 && $uppercaseOccurrences == 0 )
			$score -= 2;
		
		if ( $uppercaseOccurrences == 0 && $numberOccurrences == 0 )
			$score -= 2;
		
		if ( is_numeric($string) ) {
			
			$score -= 3;
			$messages['serious']['mentioned_numeriek'] = 'Whoa. Try using something else than numbers.';
		}
		if ( $uppercaseOccurrences <= 2 )
			$score += 0;
		elseif ( $uppercaseOccurrences <= 3 )
			$score += 1;
		elseif ( $uppercaseOccurrences > 4 )
			$score += 2;
		
		
		if ( $numberOccurrences == 0 )
			$score += 0;
		elseif ( $numberOccurrences <= 2 )
			$score += 2;
		elseif ( $numberOccurrences <= 4 )
			$score += 3;
		elseif ( $numberOccurrences > 4 )
			$score += 4;
		
		
		if ( $specialOccurrences == 0 ) {
			$score += 0;
			$messages['optional'][] = 'No special characters? Try adding some, like a space, # or %';
		}
		elseif ( $specialOccurrences <= 2 ) {
			$score += 4;
		}
		elseif ( $specialOccurrences > 2 )
			$score += 5;
		elseif ( $specialOccurrences > 4 )
			$score += 6;
		
		if ( $validateRepetition ) {
			
			$numberToCheck = ceil( strlen($string) * 0.10 );
			
			$characterCount = count_chars($string, 1);
			rsort($characterCount);
			
			$mostUsedCharacterCount = 0;
			
			for ( $c = 0; $c < $numberToCheck; $c++ ) {
				
				if ( isset($characterCount[$c]) )
					$mostUsedCharacterCount += $characterCount[$c];
			}
			
			$percentageOfEntireString = round($mostUsedCharacterCount / strlen($string) * 100);
			
			
			
			
			if ( $percentageOfEntireString <= 20 )
				$score += 1;
			elseif ( $percentageOfEntireString <= 30 )
				$score += 0;
			elseif ( $percentageOfEntireString <= 50 ) {
				$score -= 1;
				$messages['optional'][] = 'Your password repeats characters a lot. The 10% most-used characters are ' . $percentageOfEntireString . '% of the whole.';
			}
			elseif ( $percentageOfEntireString > 50 ) {
				$score -= 2;
				
				$messages['optional'][] = 'Your password repeats characters A WHOLE LOT. The 10% most-used characters are ' . $percentageOfEntireString . '% of the whole.';
			}
		}
		
		if ( $validateForActualWords ) {
			
			list($actualWordCount, $actualCharacterCount, $characterCountBiggestWord) = $this->containsActualWordsInEnglish($string);
			
			$percentageOfEntireString = round($characterCountBiggestWord / strlen($string) * 100);
			
			$wordsInDutch = false;
			$leetReferencing = false;
			
			if ( $percentageOfEntireString <= 30 && $validateForLeetSpeak ) {
				
				$leetString = $this->convertl33tspeak($string);
				
				list($actualWordCount, $actualCharacterCount, $characterCountBiggestWord) = $this->containsActualWordsInEnglish($leetString);
				
				$percentageOfEntireString = round($characterCountBiggestWord / strlen($string) * 100);
				
				$leetReferencing = true;
			}
			
			// No? Try Dutch
			if ( $percentageOfEntireString <= 30 ) {
				
				list($actualWordCount, $actualCharacterCount, $characterCountBiggestWord) = $this->containsActualWordsInDutch($string);
				
				$percentageOfEntireString = round($characterCountBiggestWord / strlen($string) * 100);
				
				$leetReferencing = false;
				$wordsInDutch = true;
				
				if ( $percentageOfEntireString <= 30 && $validateForLeetSpeak ) {
				
					$leetString = $this->convertl33tspeak($string);
					
					list($actualWordCount, $actualCharacterCount, $characterCountBiggestWord) = $this->containsActualWordsInDutch($leetString);
				
					$percentageOfEntireString = round($characterCountBiggestWord / strlen($string) * 100);
				
					$leetReferencing = true;
				}
			}
			
			
			
			if ( $percentageOfEntireString <= 30 ) {
				
			}
			elseif ( $percentageOfEntireString <= 60 ) {
				
				$messages['serious'][] = 'The password contains 1 or more actual words. The biggest word forms ' . $percentageOfEntireString . '% of the whole. Not a good idea' . ( $leetReferencing ? ' (even if you use l33t speak)' : '' ) . '.';
				$score -= 2;
			}
			elseif ( $percentageOfEntireString <= 90 ) {
				
				$messages['serious'][] = 'The password contains 1 or more actual words. The biggest word forms ' . $percentageOfEntireString . '% of the whole. Not a good idea' . ( $leetReferencing ? ' (even if you use l33t speak)' : '' ) . '.';
				$score -= 4;
			}
			elseif ( $percentageOfEntireString > 90 ) {
				
				$messages['serious'][] = 'The password contains 1 or more actual words. The biggest word forms ' . $percentageOfEntireString . '% of the whole. Not a good idea' . ( $leetReferencing ? ' (even if you use l33t speak)' : '' ) . '.';
				$score -= 10;
			}
		}
		
		
		
		return array($score, $messages);
	}


	public function passJudgement ( $score ) {
		
		if ( $score <= 0 ) {
			
			$message = 'Terrible';
			$color = '#A30E00';
		}
		elseif ( $score <= 3 ) {
			
			$message = 'Just plain bad';
			$color = '#DB3E00';
		}
		elseif ( $score <= 5 ) {
			
			$message = 'Mediocre';
			$color = '#DB5B00';
		}
		elseif ( $score <= 9 ) {
			
			$message = 'Good';
			$color = '#C8F500';
		}
		elseif ( $score > 9 ) {
			
			$message = 'Excellent';
			$color = '#7EC700';
		}
		
		return array($color, $message);
	}
	
	
	public function containsActualWordsInDutch ( $string ) {
		
		$query = sprintf("
		SELECT count(`id`) as `count`, sum(length(`word`)) as `characters`, max(length(`word`)) as `max`
		FROM `dictionary_awesome`
		WHERE \"%s\" LIKE concat( '%s', `word` , '%s' )
		",
		$this->connection->real_escape_string($string),
		'%',
		'%'
		);
		
		$resultset = $this->connection->query($query);
		
		if ( $resultset && $resultset->num_rows > 0 ) {
			
			$data = $resultset->fetch_object();
			
			if ( isset($data->count) && isset($data->characters) && isset($data->max) )
				return array($data->count, $data->characters, $data->max);
		}
		
		return array(0, 0, 0);
	}
	
	public function containsActualWordsInEnglish ( $string ) {
		
		$query = sprintf("
		SELECT count(`id`) as `count`, sum(length(`word`)) as `characters`, max(length(`word`)) as `max`
		FROM `dictionary_awesome`
		WHERE \"%s\" LIKE concat( '%s', `word` , '%s' )
		",
		$this->connection->real_escape_string($string),
		'%',
		'%'
		);
		
		$resultset = $this->connection->query($query);
		
		if ( $resultset && $resultset->num_rows > 0 ) {
			
			$data = $resultset->fetch_object();
			
			if ( isset($data->count) && isset($data->characters) && isset($data->max) )
				return array($data->count, $data->characters, $data->max);
		}
		
		return array(0, 0, 0);
	}
	
	
	public function convertl33tspeak ( $string ) {
		
		$string = str_ireplace(array('4', '/\\', '@', '/-\\'), 											'a', $string);
		$string = str_ireplace(array('I3', '8','13','|3','!3','(3','/3',')3','|-]','j3','6'), 			'b', $string);
		$string = str_ireplace(array('[', '{', '<','('), 												'c', $string);
		$string = str_ireplace(array(')', '|)','(|','[)','I>','|>','?','T)','I7','cl','|}','>','|]'), 	'd', $string);
		$string = str_ireplace(array('3','&','[-'), 													'e', $string);
		$string = str_ireplace(array('9'), 																'g', $string);
		$string = str_ireplace(array('#', '|-|'), 														'h', $string);
		$string = str_ireplace(array('1', '!'), 														'i', $string);
		$string = str_ireplace(array('0'), 																'o', $string);
		$string = str_ireplace(array('/\/\\', '/V\\','|\/|','^^','1^1'), 								'm', $string);
		
		return $string;
	}
}

?>
