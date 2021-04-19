<!DOCTYPE html>
<html lang="en">

<head>
    <title>Visualize Flowchart</title>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- jQuery & jQuery UI are required -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <!-- Flowchart CSS and JS -->
    <link rel="stylesheet" href="./js/jquery.flowchart.css">
    <script src="./js/jquery.flowchart.js"></script>


    <scrip src="./js/middle.js"></scrip>

    <style>


    </style>
</head>

<body>
  <div class="myBody">
  <h4> Flowchart </h4>
    <div id="chart_container">
        <div class="flowchart-example-container" id="flowchartworkspace"></div>
    </div>
  </div>

  <div class="myButtons">
    <div class="draggable_operators">
        <div class="draggable_operators_label">
            Operators (drag and drop them in the flowchart):
        </div>
        <div class="draggable_operators_divs">
            <div class="draggable_operator" data-nb-inputs="1" data-nb-outputs="0">1 input</div>
            <div class="draggable_operator" data-nb-inputs="0" data-nb-outputs="1">1 output</div>
            <div class="draggable_operator" data-nb-inputs="1" data-nb-outputs="1">1 input &amp; 1 output</div>
            <div class="draggable_operator" data-nb-inputs="1" data-nb-outputs="2">1 in &amp; 2 out</div>
            <div class="draggable_operator" data-nb-inputs="2" data-nb-outputs="1">2 in &amp; 1 out</div>
            <div class="draggable_operator" data-nb-inputs="2" data-nb-outputs="2">2 in &amp; 2 out</div>
        </div>
    </div>
    <button class="create_operator">Create operator</button>
    <button class="delete_selected_button">Delete selected operator / link</button>
    <div id="operator_properties" style="display: block;">
        <label for="operator_title">Operator's title: </label><input id="operator_title" type="text">
    </div>
    <div id="link_properties" style="display: block;">
        <label for="link_color">Link's color: </label><input id="link_color" type="color">
    </div>
    <button class="get_data" id="get_data">Get data</button>
    <button class="set_data" id="set_data">Set data</button>
    <button class="save_local" id="save_local">Save to local storage</button>
    <button class="load_local "id="load_local">Load from local storage</button>

    <div class="textarea">
        <textarea id="flowchart_data"></textarea>
    </div>
  </div>



    <!DOCTYPE html>
    @extends('welcome')

    <script type="text/javascript">
        $.ajax({
            method: 'GET',
            url: 'http://localhost:3000',
            async:false,
            success: function(tables) {
                let operators = []
                tables.forEach(function(item) {
                    var cols = []
                    var external_cols = []
                    var input_cols = []
                    var outputs = []
                    item.columns.forEach(function(col) {
                        cols.push(col.name)
                    })

                    if (item.foreignKeys) {
                        item.foreignKeys.forEach(function(key) {
                            key.columns.forEach(function(col, idx) {
                                external_cols.push(col.column)
                                var output = {
                                    column: col.column,
                                    table: key.reference.table,
                                    field: key.reference.columns[idx].column
                                }
                                outputs.push(output)
                            })
                        })
                    }
                    input_cols = cols.filter(x => !external_cols.includes(x));
                    let operator = {
                        name: item.name,
                        inputs: input_cols,
                        outputs: outputs,
                    }
                    operators.push(operator)
                })

                var i = 1;
                var data = {}
                var operator = "operator" + i;
                data['operators'] = {}
                data['operators'][operator] = {}

                let links_table = {}
                data['links'] = {}
                operators.forEach(function(item) {
                    let j = 1
                    let k = 1
                    data['operators'][operator] = {}
                    data['operators'][operator]['top'] = Math.random() * 600;
                    data['operators'][operator]['left'] = Math.random() * 1500;
                    data['operators'][operator]['properties'] = {}
                    data['operators'][operator]['properties']['title'] = item.name;
                    data['operators'][operator]['properties']['inputs'] = {}
                    links_table[item.name] = {
                        operator: operator,
                        inputs: [],
                        outputs: []
                    }
                    item.inputs.forEach(function(input) {
                        let inp = "input_" + j
                        data['operators'][operator]['properties']['inputs'][inp] = {
                            label: input
                        }
                        let obj = {}
                        obj[input] = inp
                        links_table[item.name].inputs.push(obj)
                        j++
                    })
                    data['operators'][operator]['properties']['outputs'] = {}
                    item.outputs.forEach(function(output) {
                        let outp = "output_" + k
                        let col = output.column
                        data['operators'][operator]['properties']['outputs'][outp] = {
                            label: col
                        }
                        let obj = {}
                        obj[col] = outp;
                        obj["table"] = output.table;
                        obj["field"] = output.field;
                        links_table[item.name].outputs.push(obj)
                        k++
                    })
                    i++;
                    operator = "operator" + i;
                })

                i = 1;
                links = {}
                operators.forEach(function(item) {
                    item.outputs.forEach(function(output) {
                        let l = "link_" + i;
                        let fromOperator = links_table[item.name].operator
                        let fromConnector = "";
                        let toOperator = "";
                        let toConnector = "";
                        links_table[item.name].outputs.forEach(function(elem) {
                            if (elem[output.column]) {
                                fromConnector = elem[output.column]
                                if (links_table[elem["table"]]) {
                                    toOperator = links_table[elem["table"]].operator
                                    links_table[elem["table"]].inputs.forEach(
                                        function(inp) {
                                            if (inp[elem["field"]]) {
                                                toConnector = inp[elem["field"]]
                                                links[l] = {
                                                    fromOperator: fromOperator,
                                                    fromConnector: fromConnector,
                                                    toOperator: toOperator,
                                                    toConnector: toConnector
                                                }
                                            }
                                        })
                                }
                            }
                        })
                        i++
                    })
                })
                data['links'] = links;
                //console.log(JSON.stringify(data));
                useReturnData(data);

            },
            error: function(request, status, error) {
                alert(request.responseText);
            }
        });

var returnedDatas;

function useReturnData(data){
    myvar = data;
    //console.log(myvar);
    returnedDatas=data;
    return myvar
};




        /* global $ */
        $(document).ready(function() {
            console.log(JSON.stringify(returnedDatas));
            var $flowchart = $('#flowchartworkspace');
            var $container = $flowchart.parent();


            // Apply the plugin on a standard, empty div...
            $flowchart.flowchart({
                data: defaultFlowchartData,
                defaultSelectedLinkColor: '#000055',
                grid: 10,
                multipleLinksOnInput: true,
                multipleLinksOnOutput: true
            });


            function getOperatorData($element) {
                var nbInputs = parseInt($element.data('nb-inputs'), 10);
                var nbOutputs = parseInt($element.data('nb-outputs'), 10);
                var data = {
                    properties: {
                        title: $element.text(),
                        inputs: {},
                        outputs: {}
                    }
                };

                var i = 0;
                for (i = 0; i < nbInputs; i++) {
                    data.properties.inputs['input_' + i] = {
                        label: 'Input ' + (i + 1)
                    };
                }
                for (i = 0; i < nbOutputs; i++) {
                    data.properties.outputs['output_' + i] = {
                        label: 'Output ' + (i + 1)
                    };
                }

                return data;
            }



            //-----------------------------------------
            //--- operator and link properties
            //--- start
            var $operatorProperties = $('#operator_properties');
            $operatorProperties.hide();
            var $linkProperties = $('#link_properties');
            $linkProperties.hide();
            var $operatorTitle = $('#operator_title');
            var $linkColor = $('#link_color');

            $flowchart.flowchart({
                onOperatorSelect: function(operatorId) {
                    $operatorProperties.show();
                    $operatorTitle.val($flowchart.flowchart('getOperatorTitle', operatorId));
                    return true;
                },
                onOperatorUnselect: function() {
                    $operatorProperties.hide();
                    return true;
                },
                onLinkSelect: function(linkId) {
                    $linkProperties.show();
                    $linkColor.val($flowchart.flowchart('getLinkMainColor', linkId));
                    return true;
                },
                onLinkUnselect: function() {
                    $linkProperties.hide();
                    return true;
                }
            });

            $operatorTitle.keyup(function() {
                var selectedOperatorId = $flowchart.flowchart('getSelectedOperatorId');
                if (selectedOperatorId != null) {
                    $flowchart.flowchart('setOperatorTitle', selectedOperatorId, $operatorTitle.val());
                }
            });

            $linkColor.change(function() {
                var selectedLinkId = $flowchart.flowchart('getSelectedLinkId');
                if (selectedLinkId != null) {
                    $flowchart.flowchart('setLinkMainColor', selectedLinkId, $linkColor.val());
                }
            });
            //--- end
            //--- operator and link properties
            //-----------------------------------------

            //-----------------------------------------
            //--- delete operator / link button
            //--- start
            $flowchart.parent().siblings('.delete_selected_button').click(function() {
                $flowchart.flowchart('deleteSelected');
            });
            //--- end
            //--- delete operator / link button
            //-----------------------------------------



            //-----------------------------------------
            //--- create operator button
            //--- start
            var operatorI = 0;
            $flowchart.parent().siblings('.create_operator').click(function() {
                var operatorId = 'created_operator_' + operatorI;
                var operatorData = {
                    top: ($flowchart.height() / 2) - 30,
                    left: ($flowchart.width() / 2) - 100 + (operatorI * 10),
                    properties: {
                        title: 'Operator ' + (operatorI + 3),
                        inputs: {
                            input_1: {
                                label: 'Input 1',
                            }
                        },
                        outputs: {
                            output_1: {
                                label: 'Output 1',
                            }
                        }
                    }
                };

                operatorI++;

                $flowchart.flowchart('createOperator', operatorId, operatorData);

            });
            //--- end
            //--- create operator button
            //-----------------------------------------




            //-----------------------------------------
            //--- draggable operators
            //--- start
            //var operatorId = 0;
            var $draggableOperators = $('.draggable_operator');
            $draggableOperators.draggable({
                cursor: "move",
                opacity: 0.7,

                // helper: 'clone',
                appendTo: 'body',
                zIndex: 1000,

                helper: function(e) {
                    var $this = $(this);
                    var data = getOperatorData($this);
                    return $flowchart.flowchart('getOperatorElement', data);
                },
                stop: function(e, ui) {
                    var $this = $(this);
                    var elOffset = ui.offset;
                    var containerOffset = $container.offset();
                    if (elOffset.left > containerOffset.left &&
                        elOffset.top > containerOffset.top &&
                        elOffset.left < containerOffset.left + $container.width() &&
                        elOffset.top < containerOffset.top + $container.height()) {

                        var flowchartOffset = $flowchart.offset();

                        var relativeLeft = elOffset.left - flowchartOffset.left;
                        var relativeTop = elOffset.top - flowchartOffset.top;

                        var positionRatio = $flowchart.flowchart('getPositionRatio');
                        relativeLeft /= positionRatio;
                        relativeTop /= positionRatio;

                        var data = getOperatorData($this);
                        data.left = relativeLeft;
                        data.top = relativeTop;

                        $flowchart.flowchart('addOperator', data);
                    }
                }
            });
            //--- end
            //--- draggable operators
            //-----------------------------------------


            //-----------------------------------------
            //--- save and load
            //--- start
            function Flow2Text() {
                var data = $flowchart.flowchart('getData');
                $('#flowchart_data').val(JSON.stringify(data, null, 2));
            }
            $('#get_data').click(Flow2Text);

            function Text2Flow() {
                var data = JSON.parse($('#flowchart_data').val());
                $flowchart.flowchart('setData', data);
            }
            $('#set_data').click(Text2Flow);

            /*global localStorage*/
            function SaveToLocalStorage() {
                if (typeof localStorage !== 'object') {
                    alert('local storage not available');
                    return;
                }
                Flow2Text();
                localStorage.setItem("stgLocalFlowChart", $('#flowchart_data').val());
            }
            $('#save_local').click(SaveToLocalStorage);

            function LoadFromLocalStorage() {
                if (typeof localStorage !== 'object') {
                    alert('local storage not available');
                    return;
                }
                var s = localStorage.getItem("stgLocalFlowChart");
                if (s != null) {
                    $('#flowchart_data').val(s);
                    Text2Flow();
                } else {
                    alert('local storage empty');
                }
            }
            $('#load_local').click(LoadFromLocalStorage);
            //--- end
            //--- save and load
            //-----------------------------------------


        });

        var defaultFlowchartData = returnedDatas;
        if (false) console.log('remove lint unused warning', defaultFlowchartData);

    </script>

</body>

</html>
