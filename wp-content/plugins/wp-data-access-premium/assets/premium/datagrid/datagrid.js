var grid;
var dataView;
var data = [];
var currentColumnHeaders;

// Set options
var options = {
    editable: true,
    enableCellNavigation: true,
    autosizeColsMode: 'LFF',
    asyncEditorLoading: true,
    resizable: true,
    scroll: true,
    sortable: true
};

// Multiple grids
// function initializeGrids(tableNames, numberOfGrids) {
//
// }

async function createDataGrid (tableName) {
    var isLastPage = false;
    
    // AJAX call
    var allAjaxCalls = new AllAjaxCalls();

    // Prepare all data
    var tableHeaders = await allAjaxCalls.getHeaders(tableName);
    currentColumnHeaders = tableHeaders[0];
    // var tableColumnTypes = tableHeaders[1];

    var tableContent = await allAjaxCalls.getContent(tableName);
    
    for (var i = 0; i < tableContent.length; i++) {
        tableContent[i].id = i;
    }
    
    // Add empty row for insert
    tableContent.push({ id: tableContent.length + 1, uniqueProp: "emptyRow" });
    
    // Add devare/create button column to grid
    var buttonToRow = addDelCreateBtnCol(currentColumnHeaders, tableContent);
    currentColumnHeaders = buttonToRow.newTableHeaders;
    
    // Create grid
    dataView = new Slick.Data.DataView({inlineFilters: true});
    console.log("dataView=", dataView);
    grid = new Slick.Grid("#myGrid", dataView, currentColumnHeaders, options);
    grid.setSelectionModel(new Slick.CellSelectionModel());
    // Pagination
    new Slick.Controls.Pager(dataView, grid, jQuery("#pager"));
    // Column picker
    new Slick.Controls.ColumnPicker(tableContent, grid, options);
    
    dataView.onRowsChanged.subscribe(function (e, args) {
        grid.invalidateRows(args.rows);
        dataView.setItems(tableContent);
        grid.render();
        jQuery("#myGrid").resizable();
    });
    
    // Pagination onChange action
    dataView.onPagingInfoChanged.subscribe(function (e, pagingInfo) {
        isLastPage = pagingInfo.pageNum === pagingInfo.totalPages - 1;
    });
    
    // Sorting
    grid.onSort.subscribe(function (e, args) {
        var dataViewEmptyRecord = dataView.getItems().find(dataItem => dataItem.uniqueProp === "emptyRow");
        
        var rowId = dataViewEmptyRecord !== undefined ? dataViewEmptyRecord.id : -1;
        if (dataView.getItemById(rowId) !== undefined) {
            dataView.deleteItem(rowId);
        }
        dataView.addItem({ id: tableContent.length + 1, uniqueProp: "emptyRow" });
        
        var comparer = function(recordA, recordB) {
            if (recordA[args.sortCol.field] !== null && recordA[args.sortCol.field] !== undefined &&
                recordB[args.sortCol.field] !== null && recordB[args.sortCol.field] !== undefined) {
    
                var sortRecordA = Object.create(recordA); // Use Object create for value typing instead of reference typing
                sortRecordA[args.sortCol.field] = recordA[args.sortCol.field].toLowerCase();
                var sortRecordB = Object.create(recordB); // Use Object create for value typing instead of reference typing
                sortRecordB[args.sortCol.field] = recordB[args.sortCol.field].toLowerCase();
                
                return (sortRecordA[args.sortCol.field] > sortRecordB[args.sortCol.field]) ? 1 : -1;
            }
        }
        dataView.sort(comparer, args.sortAsc);
    });
    
    // Initialize the model after all the events have been hooked up
    dataView.beginUpdate();
    dataView.setItems(tableContent);
    dataView.endUpdate();
    
    bindEditor(currentColumnHeaders);
    bindEditor(tableContent);
    
    jQuery("#myGrid").resizable();
}

function deleteRowFromGrid(rowNumber) {
    var selectedRecord = dataView.getItem(rowNumber);
    var ajaxCalls = new AllAjaxCalls();
    ajaxCalls.removeSelectedRecord(tableName, selectedRecord, removeSelectedRecordCallback, rowNumber);
}

// Rownumber is zero based
function createNewRow(rowNumber) {
    grid.gotoCell(rowNumber, 0, false);
    var newObject = dataView.getItem(rowNumber);
    var newRecord = { }
    
    for (var i = 0; i < currentColumnHeaders.length; ++i) {
        console.log(newObject[currentColumnHeaders[i].name]);
        
        if (newObject[currentColumnHeaders[i].name] !== undefined) {
            newRecord[currentColumnHeaders[i].name] = newObject[currentColumnHeaders[i].name];
        }
    }
    
    // AJAX call
    var ajaxCalls = new AllAjaxCalls();
    ajaxCalls.createNewRecord(tableName, newRecord, createNewRecordCallback);
}

// Creation of the delete and create buttons column
function addDelCreateBtnCol(tableHeaders, tableContent) {
    
    console.log("Add del create btn tbContent ", tableContent);
    console.log("Add del create btn tbHeaders ", tableHeaders);
    
    var actionBtnCol = {
        id: "actionBtnCol",
        name: "", // Displayname header
        field: "actionBtnCol",
        editable: false,
        behavior: "select",
        cannotTriggerInsert: true,
        resizable: false,
        selectable: false,
        cssClass: "actionBtnCol",
        formatter: actionBtnFormatter // Creates a btn in every cel of the column
    };
    tableHeaders.unshift(actionBtnCol);
    
    return { newTableHeaders: tableHeaders, newTableContent: tableContent };
}

// Formatting/creation of the buttons for the column
function actionBtnFormatter(row, cell, value, columnDef, dataContext) {
    var currentPage = grid.getData().getPagingInfo().pageNum + 1;   // Current page number (0 based)
    var definedPageSize = grid.getData().getPagingInfo().pageSize;  // Rows defined in the bottom of the table
    var currentPageSize = grid.getData().getLength();               // Rows on current page of table
    var totalPages = grid.getData().getPagingInfo().totalPages;     // Total number of pages
    var totalRows = grid.getData().getPagingInfo().totalRows;       // Total number of records in the data grid
    
    // Placing of the buttons because of paging
    
    // Multiple pages
    if (totalPages > 0)
    {
        // Last page
        if (totalPages === currentPage)
        {
            // Last row
            if (row === currentPageSize-1)
            {
                return "<label class='create-row' onclick='createNewRow(" + row + ")'>Create</label>";
            }
            else
            {
                return "<label class='delete-row' onclick='deleteRowFromGrid(" + row + ")'>Delete</label>";
            }
        }
        // Other pages
        else
        {
            return "<label class='delete-row' onclick='deleteRowFromGrid(" + row + ")'>Delete</label>";
        }
    }
    // Single paged
    else
    {
        if (row === currentPageSize)
        {
            return "<label class='create-row' onclick='createNewRow(" + row + ")'>Create</label>";
        }
        else
        {
            return "<label class='delete-row' onclick='deleteRowFromGrid(" + row + ")'>Delete</label>";
        }
    }
}

function bindEditor(tableHeaders) {
    for (var i = 0; i < tableHeaders.length; i++)
    {
        if (tableHeaders[i].id !== "actionBtnCol") {
            tableHeaders[i]['editor'] = Editor;
            tableHeaders[i]['regex'] = /^[a-zA-Z][0-9]*/;
        }
    }
}

// Ajax Callbacks
function createNewRecordCallback() {
    createDataGrid(tableName).then();
}
function updateExistingRecordCallback() {
    createDataGrid(tableName).then();
}
function removeSelectedRecordCallback(rowNumber) {
    dataView.deleteItem(dataView.getItem(rowNumber).id);
    grid.invalidate();
    grid.render();
}