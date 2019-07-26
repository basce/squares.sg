<link rel="stylesheet" href="<?=$ownfolder?>css/bootstrap-table.css">
<link rel="stylesheet" href="<?=$ownfolder?>css/bootstrap-editable.css">
<link rel="stylesheet" href="<?=$ownfolder?>css/examples.css">
<style>
.btn-file {
    position: relative;
    overflow: hidden;
}
.btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
}	
[hidden] {
  display: none !important;
}
.codetable .result{
	font-family: monospace;
	padding: 1em;
    background-color: #444;
    height: calc(100vh - 370px);
    overflow-y: auto;
}
.codetable::after{
	content:" ";
	display:block;
	padding-bottom:10px;
}
.codetable .result .output_row::before{
	content:"> ";
}
.codetable .result .output_row{
    color:deepskyblue;
}
.codetable .result .output_row strong{
	margin-right:1em;
}
.codetable .result .output_row.success{
	color:lightgreen;
}
.codetable .result .output_row.caution{
	color:orange;
}
.codetable .result .output_row.error{
	color:red;
}
.codetable .result .output_row.nochange{
    color:lightgray;
}
.codetable .result .output_row .summary{
    margin-right:0.5em;
}
.codetable .result .output_row .summary.success{
    color:lightgreen;
}
.codetable .result .output_row .summary.nochange{
    color:lightgray;   
}
.codetable .result .output_row .summary.warning{
    color:orange;   
}
.codetable .result .output_row .summary.error{
    color:red;   
}
</style>