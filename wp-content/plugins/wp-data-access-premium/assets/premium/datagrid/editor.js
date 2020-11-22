function Editor (args) {
    let $input, defaultValue;
    let scope = this;
    let allAjaxCalls = new AllAjaxCalls();
    
    this.init = function () {
        $input = jQuery("<INPUT type=text class='editor-text' />")
            .appendTo(args.container)
            .bind("keydown.nav", function (e) {
                if (e.keyCode === jQuery.ui.keyCode.LEFT || e.keyCode === jQuery.ui.keyCode.RIGHT) {
                    e.stopImmediatePropagation();
                }
            })
            .focus()
            .select();
    };
    
    this.validate = function () {
        return {
            valid: true,
            msg: null
        };
    };
    
    // On edit field exit
    this.applyValue = function (item, state) {
        let numberOfRows = grid.getData().getItems().length;
        
        console.log("numberOfRows ", numberOfRows);
        console.log("applyValue item ", item);
        console.log("applyValue state ", state);
        console.log("applyValue state ", Object.keys(item).length);
    
        let createNewValue = false;
        if (numberOfRows === item.id) {
            createNewValue = true;
        }
        
        if (!createNewValue) { // UPDATE
            let valueToChange = { };
            valueToChange[args.column.name] = state;

            // AJAX call
            allAjaxCalls.updateExistingRecord(tableName, item, valueToChange, updateExistingRecordCallback);
        }
        else { // CREATE
            item[args.column.field] = state;
        }
    };
    
    this.destroy = function () {
        $input.remove();
    };
    
    this.focus = function () {
        $input.focus();
    };
    
    this.loadValue = function (item) {
        defaultValue = item[args.column.field] || "";
        
        $input.val(defaultValue);
        $input[0].defaultValue = defaultValue;
        $input.select();
    };
    
    // On focus
    this.serializeValue = function () {
        console.log("$input", $input);
        return $input.val();
    };
    
    this.isValueChanged = function () {
        return (!($input.val() == "" && defaultValue == null)) && ($input.val() != defaultValue);
    };
    
    scope.init()
}