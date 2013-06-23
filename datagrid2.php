<?php
class DataGrid2 extends HtmlEntity
{
	private $rows;
	private $columns;
	private $dataSource;
	private $caption;
	private $rowFunction;
	private $rowClass;
	private $altRowClass;

	function __construct()
	{
		$this->rows = array();
		$this->columns = array();
		$this->dataSource = null;
	}

	public static function quickTable($array)
	{
      $keys = array_keys($array[0]);
      echo "<table border='1' style='font-size:10pt;' cellpadding='3'><tr><th>".implode("</th><th>", $keys)."</th></tr>";
      foreach ($array as $item) 
      {
        if (!is_array($item)) continue;
        echo "<tr><td>".implode("</td><td>", $item )."</td></tr>";
      }
      echo "</table>";  
	}

	public function setColumn($dataField, $headerText, $attributes=array())
	{
		$column = new Column($dataField, $headerText, $attributes);
		$this->columns[$dataField] = $column;
		return $this;
	}

	public function addColumnObj($column)
	{
		$this->columns[$column->getDataField()] = $column;
		return $this;
	}

	public function dataSource($dataSource)
	{
		$this->dataSource = $dataSource;
		return $this;
	}

	public function stripe($rowClass, $altRowClass)
	{
		$this->rowClass = $rowClass;
		$this->altRowClass = $altRowClass;
		return $this;
	}

	public function caption($caption)
	{
		$this->caption = $caption;
		return $this;
	}

	public function setRowFunction($rowFunction)
	{
		$this->rowFunction = $rowFunction;
		return $this;
	}

	public function build($print=true)
	{
		$html = "<table " . $this->getAttrString() . " >";

		if($this->caption)
		{
			$html .= "<caption>".$this->caption."</caption>";
		}

		// Build the header
		$html .= "<thead><tr>";
		foreach($this->columns as $dataField=>$headerColumn)
		{
			$html .= "<th>".$headerColumn->getHeaderText()."</th>";
		}
		$html .= "</tr></thead>";

		$html .= "<tbody>";
		
		$rowIndex = 0;

		// Create the objects and their children e.g., "row->cells->value" for ease of use in "rowFunction"
		foreach($this->dataSource as $record)
		{	
			$row = new TableRow();
			$row->setRecord($record);

			foreach($this->columns as $field=>$column)
			{
				$cellValue = (
					isset($record[$field])
					? $record[$field]
					: ""
				);

				$cell = new TableCell($field, $cellValue);
				$cell->initAttributes($column->getAttributes()); // Take default attributes set on the column and pass them down to the cell level
				$row->addCell($cell);
			}

			// Append the zebra striping classes
			if($rowIndex%2==0)
			{
				if(trim($this->rowClass))
				{
					$classes = $row->getAttr('class');
					$classes .= trim(" " . $this->rowClass);
				}
			}
			else
			{
				if(trim($this->altRowClass))
				{
					$classes = $row->getAttr('class');
					$classes .= trim(" " . $this->altRowClass);
				}
			}

			$row->setAttr('class', $classes);

			// Now that we've spun the array data into some manageable objects, let's call the rowFunction if the user provided one to format and 
			// further process, then we'll output the markup to the screen
			$rowFunctionName = $this->rowFunction;
			if(function_exists($rowFunctionName))
			{
				$row = $rowFunctionName($row);
			}

			// rowFunction/formatting complete, now let's boogie
			$html .= "<tr ".$row->getAttrString().">";
			foreach($row->getCells() as $dataField=>$cell)
			{
				$html .= "<td ".$cell->getAttrString().">".$cell->getValue()."</td>";
			}
			$html .= "</tr>";

			$rowIndex++;			
		}
		$html .= "</tbody>";
		$html .= "</table>";

		if($print) echo $html;

		return $html;
	}
}

class Column extends HtmlEntity
{
	private $headerText;
	private $dataField;

	public function __construct($dataField="", $headerText="", $attributes = array())
	{
		$this->headerText = $headerText;
		$this->dataField = $dataField;
		$this->attributes = $attributes;
	}

	public function getDataField()
	{
		return $this->dataField;
	}
	public function setDataField($datField)
	{
		$this->dataField = $dataField;
	}
	public function getHeaderText()
	{
		return $this->headerText;
	}
	public function setHeaderText($dataField)
	{
		$this->headerText = $headerText;
	}
}

class TableCell extends HtmlEntity
{
	private $value;
	private $dataField;

	public function __construct($dataField="", $value="")
	{
		$this->dataField = $dataField;
		$this->value = $value;
		$this->attributes = array();
	}

	public function getDataField()
	{
		return $this->dataField;
	}
	public function setDataField($datField)
	{
		$this->dataField = $dataField;
	}
	public function getValue()
	{
		return $this->value;
	}
	public function setValue($value)
	{
		$this->value = $value;
	}
}

class TableRow extends HtmlEntity
{
	private $cells;
	private $dataRecord;

	public function getCells()
	{
		return $this->cells;
	}

	public function addCell($cell)
	{
		$this->cells[$cell->getDataField()] = $cell;
	}

	public function getCell($dataField)
	{
		return (
			isset($this->cells[$dataField])
			? $this->cells[$dataField]
			: ""
		);
	}

	public function setRecord($record)
	{
		$this->record = $record;
	}
	public function getRecord()
	{
		return $this->record;
	}
}

class HtmlEntity
{
	protected $attributes;

	public function __construct()
	{
		$this->attributes = array();
	}
	/**
	 * Adds a css class to the collection for the row
	 * @param [string] $className [a css class name]
	 */
	/**
	 * Creates a property entry for the table and adds it to the collection for storing later.
	 * @param string $name  the name of the property e.g., id, width, cellpadding
	 * @param string $value the value of the property e.g., MyGrid, 500px, 5
	 */
	public function setAttr($nameOrArray, $value='')
	{
		if(is_array($nameOrArray))
		{
			foreach($nameOrArray as $key=>$value)
			{
				$key = strtolower($key);
				$this->attributes[$key]	= $value;	
			}
		}
		else
		{
			$key = strtolower($nameOrArray);
			$this->attributes[$key]	= $value;
		}
	
		return $this;
	}

	public function getAttr($name)
	{
		$name = strtolower($name);
		return trim(
			isset($this->attributes[$name])
			? $this->attributes[$name]
			: ""
		);
	}

	public function initAttributes($array)
	{
		$this->attributes = $array;
		
		return $this;
	}

	public function getAttributes()
	{
		return $this->attributes;
	}


	/**
	 * Removes a class from the collection for the row
	 * @param [string] $className [a css class name]
	 */
	
	/**
	 * Helper function to get a string representing all the attribute name/value pairs on a given row
	 * @return [type] [description]
	 */
	public function getAttrString()
	{
		if(!sizeof($this->attributes)) return "";
		
		$pairs = "";
		foreach($this->attributes as $key=>$value)
		{
			$pairs .= " $key=$value";
		}		

		return trim($pairs);
	}

}