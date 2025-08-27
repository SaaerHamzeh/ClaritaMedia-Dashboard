<?php
class Pager implements Iterator
{
	// Pager initialize with
	public $page;        // Current page (will be recalculated if outside valid range)
	public $perPage;     // Number of records per page
	public $numRecords;  // Total number of records
	// Pager calculates:
	public $numPages;    // Number of pages required to display $numRecords records
	public $firstRecord; // Index of first record on current page
	public $lastRecord;  // Index of last record on current page
	private $records;    // Used when iterating over object
	
	// Initialize the pager object
	public function __construct($page, $per_page, $num_records)
	{
		$this->page = $page;
		$this->perPage = $per_page;
		$this->numRecords = $num_records;
		$this->calculate();
	}

	// Do the math.
	public function calculate()
	{
		$this->numPages = ceil($this->numRecords / $this->perPage);
		if($this->numPages == 0) $this->numPages = 1;
		$this->page = intval($this->page);
		if($this->page < 1) $this->page = 1;
		if($this->page > $this->numPages) $this->page = $this->numPages;
		$this->firstRecord = (int) ($this->page - 1) * $this->perPage;
		$this->lastRecord  = (int) $this->firstRecord + $this->perPage - 1;
		if($this->lastRecord >= $this->numRecords) $this->lastRecord = $this->numRecords - 1;
		$this->records = range($this->firstRecord, $this->lastRecord, 1);
	}

	// Will return current page if no previous page exists
	public function prevPage()
	{
		return max(1, $this->page - 1);
	}

	// Will return current page if no next page exists
	public function nextPage()
	{
		return min($this->numPages, $this->page + 1);
	}

	// Is there a valid previous page?
	public function hasPrevPage()
	{
		return $this->page > 1;
	}

	// Is there a valid next page?
	public function hasNextPage()
	{
		return $this->page < $this->numPages;
	}

	#[\ReturnTypeWillChange]
	public function rewind()
	{
		reset($this->records);
	}

	public function current(): mixed
	{
		return current($this->records);
	}

	public function key(): mixed
	{
		return key($this->records);
	}

	#[\ReturnTypeWillChange]
	public function next()
	{
		return next($this->records);
	}

	public function valid(): bool
	{
		return $this->current() !== false;
	}
}
?>
