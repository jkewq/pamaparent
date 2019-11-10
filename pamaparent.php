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
	
	public function __construct(string $opening='[', string $closing=']') {
		if (mb_strlen($opening) != 1 || mb_strlen($closing) != 1) {
			throw new Exception('The length of opening and closing items must be exactly 1 character');	
		}
		$this->_opening = $opening;
		$this->_closing = $closing;
	}
	
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
	
	public function getOpeningPositions(): array {
		return $this->_openingPositions;
	}
	
	public function getClosingPositions(): array {
		return $this->_closingPositions;
	}
	
	public function getPairPositions(): array {
		return $this->_pairPositions;
	}
	
	public function getRightJumpIndexes(): array {
		return $this->_rightJumpIndexes;
	}
	
	public function getLeftJumpIndexes(): array {
		return $this->_leftJumpIndexes;
	}
	
	public function getMaxLevel(): int {
		return max(array_keys($this->_contents));
	}
	
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