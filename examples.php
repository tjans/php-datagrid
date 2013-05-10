<link rel="stylesheet" href="examples.css" />
<?php
	require 'datagrid.php';

	$flavors = array(
		array(
			"Id"=>"1",
			"Name"=>"Vanilla",
			"Type"=>1
		),
		array(
			"Id"=>"2",
			"Name"=>"Chocolate",
			"Type"=>1
		),
		array(
			"Id"=>"3",
			"Name"=>"Strawberry",
			"Type"=>1
		),
		array(
			"Id"=>"4",
			"Name"=>"Banana",
			"Type"=>1
		),
		array(
			"Id"=>"5",
			"Name"=>"Mint",
			"Type"=>1
		)
	);

	$grid = new DataGrid();
	$grid
		-> dataSource($flavors)
		-> rowFunction('build_row')
		-> headerRowFunction('build_header_row')
		-> rowClass('itemRow')
		-> altRowClass('altRow')
		-> headerClass('headerRow')
		-> caption('5 Awesome Ice Cream Flavors')
		-> setProp('id', 'MyGrid')
		-> setProp('border', 1);

		// An example of adding the column via an object for more flexibility
		$column = new Column('Id', 'ID');
		
		$output = $grid
			->addColumnObj($column)
			->addColumn('Name', 'Item Name', 'Name')	// adding via quick-add method, most columns will work this way
			->addColumn('Type', 'Item Type', 'Type')
			->addColumn('Action', 'Actions')
			->build();

function build_header_row($columns)
{
	$columns['Name']->headerText = "<a href='#'>Name</a>"; // maybe a sort url or something
	return $columns;
}

function build_row($row)
{
	// Setup some values for some of the data fields
	$name = $row->getVal('Name');
	$id = $row->getVal('Id');

	// Setup the columns you want to work with
	$idCol = $row->getCol('Id');
	$nameCol = $row->getCol('Name');
	$actionCol = $row->getCol('Action');
	$typeCol = $row->getCol('Type');

	// Set some HTML properties on the give column
	$idCol->setProp('width','50');
	$typeCol->setProp('width','50');

	// Add some CSS classes to the action column
	$actionCol
		-> addClass('center')
		-> addClass('btn');

	// Add an action link to the Action column
	$row->addLink('Action', "examples.php?id=$id", '[edit]', "", "no-un");
	$row->addLink('Action', "examples.php?id=$id", '[delete]', "", "no-un");

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

	// change one of the cell values
	if($name == "Banana")	
	{
		$row->setVal('Name', $name . ' (favorite)');
	}

	return $row;
}
?>