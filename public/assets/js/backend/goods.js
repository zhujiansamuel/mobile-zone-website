define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // テーブルパラメーター設定の初期化
            Table.api.init({
                extend: {
                    index_url: 'goods/index' + location.search,
                    add_url: 'goods/add',
                    edit_url: 'goods/edit',
                    del_url: 'goods/del',
                    multi_url: 'goods/multi',
                    import_url: 'goods/import',
                    table: 'goods',
                }
            });

            var table = $("#table");

            // テーブルの初期化
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                fixedColumns: true,
                fixedRightNumber: 1,
                showToggle:false,
                showExport: false,
                showColumns: false,
                search: false,
                queryParams : function(params)
                {
                    params.with = 'category,second,three';
                    return params;
                },
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {
                            field: 'recomm',
                            title: __('トップページ固定表示'),
                            align: 'center',
                            table: table,
                            formatter: Table.api.formatter.toggle
                        },
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"0":__('Status 0')}, formatter: Table.api.formatter.status},
                        {field: 'category.name', title: __('Category_id')},
                        {field: 'second.name', title: __('Category_second')},
                        {field: 'three.name', title: __('Category_three')},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        {field: 'weigh', title: __('優先度ソート'), operate: false},
                        //{field: 'memo', title: __('memo'), operate: 'LIKE'},
                        {field: 'memo', title: __('備考'), operate: 'LIKE'},
                        {field: 'imei', title: __('IMEI'), operate: 'LIKE'},
                        
                        //{field: 'color_id', title: __('Color_id'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        //{field: 'spec_info', title: __('Spec_info'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        
                        //{field: 'price_zg', title: __('Price_zg'), operate:'BETWEEN'},
                        //{field: 'type', title: __('type'), searchList: {"1":__('新品'),"2":__('中古')}, formatter: Table.api.formatter.status},
                        
                        
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // テーブルにイベントをバインド
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };

    $("#c-category_second").data("params", function (obj) {
        var v = $("#c-category_id").val();
        v = v > 0 ? v : -1;
        //objためのSelectPageオブジェクト
        return {custom: {pid: v}};
    });

    $("#c-category_three").data("params", function (obj) {
        var v = $("#c-category_second").val();
        v = v > 0 ? v : -1;
        //objためのSelectPageオブジェクト
        return {custom: {pid: v}};
    });
    return Controller;
});
