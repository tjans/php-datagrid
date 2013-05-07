<p>Previously, I had written a php data grid that worked fine, but as requirements grew, I realized how inflexible it actually was.  This project is the response to that, and should prove to be a little more flexible, as it's written with a more object-oriented approach.  It's currently a work-in-progress, but should at it's current state be a suitable class for creating an HTML grid from PHP.</p>

<p>Please view the examples.php for usage examples.</p>

<pre><code>&lt;link rel="stylesheet" href="examples.css" /&gt;
&lt;?php
require 'datagrid.php';

$flavors = array(
    array(
        "Id"=&gt;"1",
        "Name"=&gt;"Vanilla"
    ),
    array(
        "Id"=&gt;"2",
        "Name"=&gt;"Chocolate"
    ),
    array(
        "Id"=&gt;"3",
        "Name"=&gt;"Strawberry"
    ),
    array(
        "Id"=&gt;"4",
        "Name"=&gt;"Banana"
    ),
    array(
        "Id"=&gt;"5",
        "Name"=&gt;"Mint"
    )
);

$grid = new DataGrid();
$grid
    -&gt; dataSource($flavors)
    -&gt; rowFunction('build_row')
    -&gt; altRowClass('altRow')
    -&gt; setProp('id', 'MyGrid')
    -&gt; setProp('border', 1)
    -&gt; setProp('width', '300px');

    // An example of adding the column via an object for more flexibility
    $column = new Column('Id', 'ID');

    $output = $grid
        -&gt;addColumnObj($column)
        -&gt;addColumn('Name', 'Item Name')    // adding via quick-add method, most columns will work this way
        -&gt;build();

function build_row($row)
{
// Setup some values for some of the data fields
$name = $row-&gt;getVal('Name');
$id = $row-&gt;getVal('Id');

$nameCol = $row-&gt;getCol('Name');

// Set an ID on the row
$row-&gt;setProp('id', "tr_$id");

// set the background of the strawberry column to black
if($name == "Strawberry")
{
    // get an instance of the column and set a css class
    $nameCol-&gt;addClass('strawberry');
}

// set the background of the entire shoe row to red
if($name == "Chocolate")    
{
    $row-&gt;addClass('chocolate');
}

// change one of the values
if($name == "Banana")   
{
    $row-&gt;setVal('Name', $name . ' (favorite)');
}

return $row;
}
?&gt;
</code></pre>

