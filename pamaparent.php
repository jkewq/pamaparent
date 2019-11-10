<?php

class PamaParent {

	private $_actualLevel = -1;
	private $_openingPositions = [];
	private $_closingPositions = [];
	private $_pairPositions = [];
	private $_rightJumpIndexes = [];
	private $_leftJumpIndexes = [];
	private $_contents = [];
	private $_opening;
	private $_closing;
	private $_successStatus;
	private $_errorType;
	private $_errorPosition;
	
	/**
	 * Takes the opening and closing elements as arguments and checks if they are 1 char long
	 *
	 * @param string $opening opening element (1 char long)
	 * @param string $closing closing element (1 char long)
	 * @return void
	 */
	public function __construct(string $opening='[', string $closing=']') {
		if (mb_strlen($opening) != 1 || mb_strlen($closing) != 1) {
			throw new Exception('The length of opening and closing items must be exactly 1 character');	
		}
		$this->_opening = $opening;
		$this->_closing = $closing;
	}
	
	/**
	 * Parsing the string supplied as argument
	 *
	 * @param string $input string to parse
	 * @return bool success status - true if successfully parsed, false otherwise
	 */
	public function parse(string $input): bool {
		$this->_successStatus = true;
		$levelBegins = [];
		$buffer = [];
		$len = mb_strlen($input);
		for($ptr=0;$ptr<$len;$ptr++) {
			$token = $input[$ptr];
			$this->_bufferingContent($buffer, $token);
			if ($token == $this->_opening) {
				$this->_actualLevel++;
				array_push($this->_openingPositions, $ptr);
				$levelBegins[$this->_actualLevel] = $ptr;
			} elseif ($token == $this->_closing) {
				$this->_saveActualBufferLevel($buffer);
				$this->_emptyActualBufferLevel($buffer);
				array_push($this->_closingPositions, $ptr);
				array_push($this->_pairPositions, [$levelBegins[$this->_actualLevel], $ptr]);
				$this->_rightJumpIndexes[$levelBegins[$this->_actualLevel]] = $ptr;
				$this->_leftJumpIndexes[$ptr] = $levelBegins[$this->_actualLevel];
				$this->_actualLevel--;
			} 
		}
		return $this->_successStatus;
	}
	
	/**
	 * Returns an array containing the positions of the opening elements (indexed from zero)
	 *
	 * @return array positions of the opening elements (indexed from zero)
	 */
	public function getOpeningPositions(): array {
		return $this->_openingPositions;
	}
	
	/**
	 * Returns an array containing the positions of the closing elements (indexed from zero)
	 *
	 * @return array positions of the closing elements (indexed from zero)
	 */
	public function getClosingPositions(): array {
		return $this->_closingPositions;
	}
	
	/**
	 * Returns an array containing positions of matching pairs [opening element, closing element]
	 *
	 * @return array positions of matching pairs
	 */
	public function getPairPositions(): array {
		return $this->_pairPositions;
	}
	
	/**
	 * Returns an associative array where the key is the position of the opening element and the value is the position of the matching
	 * closing element
	 *
	 * @return array 
	 */
	public function getRightJumpIndexes(): array {
		return $this->_rightJumpIndexes;
	}
	
	/**
	 * Returns an associative array where the key is the position of the closing element and the value is the position of the matching
	 * opener element
	 *
	 * @return array
	 */
	public function getLeftJumpIndexes(): array {
		return $this->_leftJumpIndexes;
	}
	
	/**
	 * Returns the index of the last level (levels are indexed from zero)
	 *
	 * @return int
	 */
	public function getMaxLevel(): int {
		return max(array_keys($this->_contents));
	}
	
	/**
	 * 
	 */
	public function getContentsByLevel(int $level=null): array {
		if (is_null($level) === FALSE) {
			return $this->_contents[$level];	
		}
		ksort($this->_contents, SORT_NUMERIC);
		return $this->_contents;
	}
	
	public function getSuccessStatus(): ?bool {
		return $this->_successStatus;
	}
	
	public function getErrorType(): string {
		return $this->_errorType;
	}
	
	public function getErrorPosition(): ?int {
		return $this->_errorPosition;
	}
	
	private function _bufferingContent(array &$buffer, string $token): void {
		$level = $this->_actualLevel;
		if ($token == $this->_opening) {
			$level++;	
		}
		if ($level >= 0) {
			for($i=$level;$i>=0;$i--) {
				if ($i == $level && ($token == $this->_opening || $token == $this->_closing)) {
					continue;	
				}
				if (empty($buffer[$i]) === TRUE) {
					$buffer[$i] = '';	
				}
				$buffer[$i] .= $token;	
			}
		}
	}
	
	private function _saveActualBufferLevel(array &$buffer): void {
		if (empty($this->_contents[$this->_actualLevel]) === TRUE || is_array($this->_contents[$this->_actualLevel]) === FALSE) {
			$this->_contents[$this->_actualLevel] = [];	
		}
		array_push($this->_contents[$this->_actualLevel], $buffer[$this->_actualLevel]);
	}
	
	private function _emptyActualBufferLevel(array &$buffer): void {
		$buffer[$this->_actualLevel] = '';
	}
	
}