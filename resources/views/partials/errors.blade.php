
@if (isset($message))
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span>
        </button>
        {{ $message }}</div>
@endif
@if (isset($error))
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span>
        </button>
        {{ $error }}</div>
@endif
@if($errors->any())
    <div class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span>
    </button>
        {{$errors->first()}}</div>
@endif