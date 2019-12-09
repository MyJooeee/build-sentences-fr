<?php

require_once('class/AbstractData.php');

class WordsStatisticsService extends AbstractData
{
	protected $file;
	protected $arrayStats = [];

	public function __construct($file)
	{
		$this->file = $file;
	}

	public function setDataFromCSVFile()
	{
		if (($handle = fopen($this->file, "r")) !== FALSE) {
    		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

	        $this->analyzeWord($data[0]);

	    	}

    	fclose($handle);

		}

		$this->finalizeData();

		// Appel méthode abstraite
		$this->setData($this->sortArray());
	}

	protected function analyzeWord($word)
	{
		$splitedWord = str_split($word);
		$length = strlen($word);

		for($i=0; $i < $length; $i++) {


			// Si le caractère suivant n'est pas défini
			if(!isset($splitedWord[$i+1])) {
				continue;
			}

			$currentCharact = strtolower(htmlspecialchars($splitedWord[$i]));
			$nextCharact = strtolower(htmlspecialchars($splitedWord[$i+1]));


			// Si le caractère courant ou le suivant n'a pas un bon format
			if(empty($currentCharact) || empty($nextCharact) || in_array('-', [$currentCharact, $nextCharact])) {
				continue;
			}

			// Entrée lettre vue pour la première fois
			if(!isset($this->arrayStats[$currentCharact])) {

				$this->arrayStats[$currentCharact] = [];
			}

			// Si binôme de lettres jamais encore était rencontré
			if(!isset($this->arrayStats[$currentCharact][$nextCharact])) {

				$this->arrayStats[$currentCharact][$nextCharact] = 0;
			}

			$this->arrayStats[$currentCharact][$nextCharact] += 1;
		}
	}

	protected function finalizeData()
	{
		foreach ($this->getAlphabet() as $key => $firstLevel) {
			foreach ($this->getAlphabet() as $key => $secondLevel) {

				if(empty($this->arrayStats[$firstLevel][$secondLevel])) {
					$this->arrayStats[$firstLevel][$secondLevel] = 0;
				}
			}
		}

		return $this->arrayStats;
	}

	public function sortArray($data = null) 
	{

		if(is_null($data)) {
			$data = $this->arrayStats;
		}

		ksort($data);
	

		foreach ($data as &$subData) { // passage par référence : subdata de data est trié alphabétiquement
			ksort($subData);
		}

		return $data;
	}

}