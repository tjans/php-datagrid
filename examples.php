<link rel="stylesheet" href="examples.css" />
<?php
	require 'datagrid.php';

	$flavors = array(
		array(
			"Id"=>"1",
			"Name"=>"Vanilla"
		),
		array(
			"Id"=>"2",
			"Name"=>"Chocolate"
		),
		array(
			"Id"=>"3",
			"Name"=>"Strawberry"
		),
		array(
			"Id"=>"4",
			"Name"=>"Banana"
		),
		array(
			"Id"=>"5",
			"Name"=>"Mint"
		)
	);

	$grid = new DataGrid();
	$grid
		-> dataSource($flavors)
		-> rowFunction('build_row')
		-> altRowClass('altRow')
		-> setProp('id', 'MyGrid')
		-> setProp('border', 1)
		-> setProp('width', '300px');

		// An example of adding the column via an object for more flexibility
		$column = new Column('Id', 'ID');
		
		$output = $grid
			->addColumnObj($column)
			->addColumn('Name', 'Item Name')	// adding via quick-add method, most columns will work this way
			->build();

function build_row($row)
{
	// Setup some values for some of the data fields
	$name = $row->getVal('Name');
	$id = $row->getVal('Id');

	$nameCol = $row->getCol('Name');

	// Set an ID on the row
	$row->setProp('id', "tr_$id");

	// set the background of the strawberry column to black
	if($name == "Strawberry")
	{
		// get an instance of the column and set a css class
		$nameCol->addClass('strawberry');
	}

	// set the background of the entire shoe row to red
	if($name == "Chocolate")	
	{
		$row->addClass('chocolate');
	}

	// change one of the values
	if($name == "Banana")	
	{
		$row->setVal('Name', $name . ' (favorite)');
	}

	return $row;
}
?>