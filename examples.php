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

	// determine the names of the querystring parameters for sorting (or post params if desired)
	$sortFieldParam = 'sf';
	$sortDirParam = 'sd';

	// setup the current sort field and direction
	$sortField = (
		isset($_GET[$sortFieldParam])
		? $_GET[$sortFieldParam]
		: null
	);

	$sortDir = (
		isset($_GET[$sortDirParam])
		? $_GET[$sortDirParam]
		: null
	);

	$grid = new DataGrid();
	$grid
		-> dataSource($flavors)
		-> rowFunction('build_row')
		-> altRowClass('altRow')
		-> headerClass('headerRow')
		-> setProp('id', 'MyGrid')
		-> setProp('border', 1)
		-> setSort($sortField, $sortDir, $sortFieldParam, $sortDirParam);

		// An example of adding the column via an object for more flexibility
		$column = new Column('Id', 'ID');
		
		$output = $grid
			->addColumnObj($column)
			->addColumn('Name', 'Item Name', 'Name')	// adding via quick-add method, most columns will work this way
			->addColumn('Type', 'Item Type', 'Type')
			->addColumn('Action', 'Actions')
			->build();

function build_row($row)
{
	// Setup some values for some of the data fields
	$name = $row->getVal('Name');
	$id = $row->getVal('Id');

	$idCol = $row->getCol('Id');
	$nameCol = $row->getCol('Name');
	$actionCol = $row->getCol('Action');
	$typeCol = $row->getCol('Type');

	$idCol->setProp('width','50');
	$typeCol->setProp('width','50');

	$actionCol
		-> addClass('center')
		-> addClass('btn');

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

	// change one of the values
	if($name == "Banana")	
	{
		$row->setVal('Name', $name . ' (favorite)');
	}

	return $row;
}
?>