<?php

namespace CodeMeme\RulesBundle\Rule\Comparator;

class Contains extends AbstractComparator
{

	public function compare($actual)
	{
		if (is_array($actual)) {
			return in_array($this->expected, array_map(function($array) {
				return (string) $array;
			}, $actual));
		}

		if ($actual instanceof \IteratorAggregate) {
			$data = iterator_to_array($actual);

			return array_map(function($array) {
				return (string) $array;
			}, $data);
		}

		return stripos($actual, $this->expected) !== false;
	}

}