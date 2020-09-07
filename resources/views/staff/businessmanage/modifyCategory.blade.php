<form id="modify_form" class="form-horizontal">
    <input type="hidden" name="id" value="{{$data['id']}}" />
    <div class="form-group">
        <label class="col-sm-4 control-label">选择分类：</label>
        <div class="col-sm-6">
            <select @if($data['pid'] == 0)disabled="disabled"@endif id="modify_id" name="tag" class="firstClass">
                <option value="2" @if($data['pid'] == 0)selected="selected"@endif>一级分类@if($data['pid'] == 0)(禁止修改)@endif</option>
                <option value="1" @if($data['pid'] != 0)selected="selected"@endif>二级分类</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">选择所属一级分类：</label>
        <div class="col-sm-6">
            <select name="pid" id="secondClass">
                @forelse($pCategory as $val)
                    <option value="{{$val['id']}}" @if($val['id']==$data['pid'])selected="selected"@endif>{{$val['title']}}</option>
                    @endforeach
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="className_1" class="col-sm-4 control-label">填写分类名称：</label>
        <div class="col-sm-6">
            <input name="title" type="text" class="form-control classify" id="className_1" placeholder="请输入分类名称" value="{{$data['title']}}">
        </div>
    </div>
    <div class="form-group">
        <label for="className" class="col-sm-4 control-label">填写分类排序值：</label>
        <div class="col-sm-6">
            <input name="sort" type="text" class="form-control classify" id="className" placeholder="请输入排序值" value="{{$data['sort']}}">
        </div>
    </div>
</form>