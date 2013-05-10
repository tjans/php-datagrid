<?php
/**
 * A PHP class for rendering a table 
 */
class DataGrid extends HtmlProperty
{
	private $columns;
	private $rows;
	private $headerRow;
	private $html;
	private $rowFunctionName;
	private $headerRowFunctionName;
	private $rowClass;
	private $alternateRowClassName;
	private $headerRowClassName;
	private $sortDirection;
	private $sortField;
	private $showHeader=true;

	private $sortFieldParameter;
	private $sortDirectionParameter;

	public $dataSource;

	public function __construct()	
	{
		$this->columns = array();
		$this->rows = array();

		return $this;
	}

	/**
	 * Provides a function to be run on the data before it is built and added to the table markup.
	 * @param  string $functionName the name of your custom function to be run
	 * @return DataGrid instance for chaining purposes
	 */
	public function rowFunction($functionName)
	{
		$this->rowFunctionName = $functionName;
		return $this;		
	}

	/**
	 * Provides a function to be run on the header data before it is built and added to the table markup.
	 * @param  string $functionName the name of your custom function to be run
	 * @return DataGrid instance for chaining purposes
	 */
	public function headerRowFunction($functionName)
	{
		$this->headerRowFunctionName = $functionName;
		return $this;		
	}

	/**
	 * Sets whether the header should display
	 * @param  [bool] $showHeader
	 * @return [instance of DataGrid for chaining]
	 */
	public function showHeader($showHeader)
	{
		$this->showHeader = $showHeader;
		return $this;
	}

	/**
	 * Sets the datasource that the grid will build its rows from
	 * @param  array $dataSource - a multi-dim array of associative arrays
	 * @return DataGrid instance for chaining purposes
	 */
	public function dataSource($dataSource)
	{
		$this->dataSource = $dataSource;
		return $this;
	}

	/**
	 * Defines the normal class for a standard row
	 * @param  string $className - name of the css class you want to include
	 * @return DataGrid instance for chaining purposes
	 */
	public function rowClass($className="")
	{
		$this->rowClass = $className;
		return $this;
	}

	/**
	 * Set the sort field and direction for the grid
	 * @param  [string] $field     [sort field name]
	 * @param  [string] $direction [asc/desc]
	 * @return [DataGrid instanace for chaining purposes]
	 */
	public function setSort($field, $direction, $sortFieldParameter="sort_field", $sortDirectionParameter="sort_direction")
	{
		$this->sortField = $field;
		$this->sortDirection = $direction;
		$this->sortFieldParameter = $sortFieldParameter;
		$this->sortDirectionParameter = $sortDirectionParameter;
		return $this;
	}

	/**
	 * Defines the class for an alternate grid row
	 * @param  string $className - name of the css class you want to include
	 * @return DataGrid instance for chaining purposes
	 */
	public function altRowClass($className="")
	{
		$this->alternateRowClassName = $className;
		return $this;
	}

	/**
	 * Defines the class for an alternate grid row
	 * @param  string $className - name of the css class you want to include
	 * @return DataGrid instance for chaining purposes
	 */
	public function headerClass($className="")
	{
		$this->headerRowClassName = $className;
		return $this;
	}

	/**
	 * This is the function that builds the html for the grid.
	 * @param  boolean $print - determines whether or not to display the grid after building
	 * @return string - the string representing the grid's HTML
	 */
	public function build($print = true)
	{
		$this->html = "";

		// Build the property string for the table tag
		$pairs = "";
		foreach($this->props as $key=>$value)
		{
			$pairs .= " $key=$value";
		}

		$this->append("<table$pairs>");

		$headerRowClassName = (
			$this->headerRowClassName
			? "class='".$this->headerRowClassName."'"
			: ""
		);

		// // Build the rows collection for the body
		// if(sizeof($this->dataSource))
		// {
		// 	$rowNumber = 0;

		// 	foreach($this->dataSource as $dataRow)
		// 	{
		// 		$isAlternate = $rowNumber%2;

		// 		$row = new Row();
		// 		if($isAlternate) $row->addClass($this->alternateRowClassName);

		// 		// loop through the values in the data source and set the values on the row
		// 		foreach($this->columns as $column)
		// 		{
		// 			// This section is used to determine if you'd added a custom field to the data source
		// 			// e.g., adding a column for a button
		// 			$key = $column->dataField;
		// 			$value = (
		// 				array_key_exists($key, $dataRow)
		// 				? $dataRow[$key]
		// 				: ""
		// 			);
		// 			$row->setVal($key, $value);
		// 		}
		// 		$row->rowNumber = $rowNumber;
		// 		$this->rows[$rowNumber] = $row;

		// 		$rowNumber++;
		// 	}
		// }

		// Build the header
		if($this->showHeader)
		{
			// Build the header
			$headerHtml = "<thead><tr $headerRowClassName>";	

			if(function_exists($this->headerRowFunctionName))
			{
				$functionName = $this->headerRowFunctionName;
				$this->columns = $functionName($this->columns);
			}
			foreach($this->columns as $column)
			{
				$headerHtml .= "<th>$column->headerText</th>";
			}

			$headerHtml .= "</tr></thead>";	

			$this->append($headerHtml);
		}

		// Build the rows collection for the body
		if(sizeof($this->dataSource))
		{
			$this->append('<tbody>');
			$rowNumber = 0;

			foreach($this->dataSource as $dataRow)
			{
				$isAlternate = $rowNumber%2;

				$row = new Row();
				if($isAlternate) $row->addClass($this->alternateRowClassName);

				// loop through the values in the data source and set the values on the row
				foreach($this->columns as $column)
				{
					// This section is used to determine if you'd added a custom field to the data source
					// e.g., adding a column for a button
					$key = $column->dataField;
					$value = (
						array_key_exists($key, $dataRow)
						? $dataRow[$key]
						: ""
					);
					$row->setVal($key, $value);
				}
				$row->rowNumber = $rowNumber;
				$this->rows[$rowNumber] = $row;

				// here is where you'd call the row_bound function if it exists. That function takes care of formatting the data, changing it, etc. It passes a "row" object to that function 
				if(function_exists($this->rowFunctionName))
				{
					$functionName = $this->rowFunctionName;
					$row = $functionName($row);
				}
				
				$this->append("<tr class='". $row->getClassString() ."' " . $row->getPropString() . ">");
				foreach($this->columns as $column)
				{
					$valueColumn = $row->getCol($column->dataField);
					$this->append("<td class='".$valueColumn->getClassString()."' " . $valueColumn->getPropString() . ">");
					$val = $row->getVal($column->dataField);
					$this->append($val);

					$this->append('</td>');
				}
				$this->append('</tr>');

				$rowNumber++;
			}

			$this->append('</tbody>');
		}

		// finish off the table
		$this->append('</table>');

		if($print) echo $this->html;

		return $this->html;
	}

	/**
	 * Private helper for appinding to the main HTML of the grid
	 * @param  string $text
	 */
	private function append($text)
	{
		$this->html .= $text;
	}

	/**
	 * A quick-add helper function that adds a new column to the collection
	 * @param string $dataField  The name of the datafield you want to display in that column. (must match a column in the datasource)
	 * @param string $headerText The friendly name you want displayed in the header for a given column
	 */
	public function addColumn($dataField, $headerText, $sortField = null)
	{
		$column = new Column($dataField, $headerText, $sortField);
		$this->columns[$dataField] = $column;
		return $this;
	}

	/**
	 * A function that allows greater flexibility and column customization by taking in a column object. This would get used
	 * if the user wanted to set a width, or other properties.
	 * @param Column [obj] $column an instance of a column
	 */
	public function addColumnObj($column)
	{
		$this->columns[$column->dataField] = $column;
		return $this;
	}
}
/**
 * The template for a column
 */
class Column extends HtmlProperty
{
	public $headerText;
	public $dataField;
	public $sortField;

	public function __construct($dataField, $headerText, $sortField=null)
	{
		$this->headerText = $headerText;
		$this->dataField = $dataField;	
		$this->sortField = $sortField;
	}
}

class ValueColumn extends HtmlProperty
{
	public $value;

	public function __construct()
	{
		parent::__construct();
		$this->classes = array();
	}
}

/**
 * Class that represents a row for the table
 */
class Row extends HtmlProperty
{
	private $values;
	public $rowNumber;

	public function __construct()
	{
		parent::__construct();
		$this->values = array();
	}

	public function addLink($columnName, $url, $text, $id="", $classes="")
	{
		$html = $this->getVal($columnName);
		if($html) $html .= "&nbsp;";
		$html .= "<a href='$url' id='$id' class='$classes'>$text</a>";
		$this->setVal($columnName, $html);
	}

	// public function addButton($columnName, $value, $name, $id="", $classes="")
	// {
	// 	$html = $this->getVal($columnName);
	// 	if($html) $html .= "&nbsp;";
	// 	$html .= "<input type='button' name='$name' value='$value' id='$id' classes='$classes' />";
	// 	$this->setVal($columnName, $html);
	// }

	/**
	 * Sets the value of a given field on the row
	 * @param [string] $dataField [name of the field from the data source]
	 * @param [ValueColumn obj] $value [an instance of the ValueColumn class]
	 */
	public function setVal($dataField, $value=null)
	{		
		if(array_key_exists($dataField, $this->values))
		{
			$valueColumn = $this->values[$dataField];
		}
		else
		{
			$valueColumn = new ValueColumn();
		}

		$valueColumn->value = $value;

		$this->values[$dataField] = $valueColumn;
		return $this;
	}

	/**
	 * Returns a ValueColumn object represented by a field from the data source
	 * @param  [string] $dataField [name of the field from the data source]
	 * @return [ValueColumn obj] $value [an instance of the ValueColumn class]
	 */
	public function getVal($dataField)
	{
		$value = null;

		if(array_key_exists($dataField, $this->values))
		{
			$value = $this->values[$dataField]->value;
		}
		return $value;
	}

	/**
	 * Gets an instance of the ValueColumn object associated with the given name
	 * @param  [type] $name [description]
	 * @return [ValueColumn]
	 */
	public function getCol($dataField)
	{
		if(array_key_exists($dataField, $this->values))
		{
			$col = $this->values[$dataField];
		}
		return $col;
	}
}

class HtmlProperty
{
	protected $classes;
	protected $props;

	public function __construct()
	{
		$this->classes = array();
		$this->props = array();
	}
	/**
	 * Adds a css class to the collection for the row
	 * @param [string] $className [a css class name]
	 */
	public function addClass($className)
	{
		$this->classes[] = $className;
		return $this;
	}

	/**
	 * Creates a property entry for the table and adds it to the collection for storing later.
	 * @param string $name  the name of the property e.g., id, width, cellpadding
	 * @param string $value the value of the property e.g., MyGrid, 500px, 5
	 */
	public function setProp($name, $value='')
	{
		$this->props[$name]	= $value;
		return $this;
	}
	
	/**
	 * Removes a class from the collection for the row
	 * @param [string] $className [a css class name]
	 */
	public function removeClass($className)
	{
		if($key=array_search($className, $this->classes) !== false)		
		{
			unset($this->classes[$key]);
		}
	}

	/**
	 * Helper function to get a space-delimited list of css class names
	 * @return [type] [description]
	 */
	public function getClassString()
	{
		return implode(' ', $this->classes);
	}

	/**
	 * Helper function to get a string representing all the attribute name/value pairs on a given row
	 * @return [type] [description]
	 */
	public function getPropString()
	{
		$pairs = "";
		foreach($this->props as $key=>$value)
		{
			$pairs .= " $key=$value";
		}		

		return trim($pairs);
	}

}