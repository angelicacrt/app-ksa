<div class="flex-wrap align-items-center mb-3 mt-3 ml-3 rcorners1 w-50">
    @if((int)date("H") >= 18)
        <h2 class="med-query">Malam, {{ Auth::user()->name }} !</h2>
    @elseif((int)date("H") >= 15)
        <h2 class="med-query">Sore, {{ Auth::user()->name }} !</h2>
    @elseif((int)date("H") >= 12)
        <h2 class="med-query">Siang, {{ Auth::user()->name }} !</h2>
    @else
        <h2 class="med-query">Pagi, {{ Auth::user()->name }} !</h2>
    @endif
    <h3 class="med-query">{{ "Today is " . date('l') . ', ' . date('d M Y')}} <span id="txt"></span></h3>
</div>

<style>
    .rcorners1 {
        border-radius: 25px;
        /* background-color: rgba(255, 0, 0, 0.5); */
        background-color: rgba(245, 97, 97, 0.7);
        padding: 20px;
    }
</style>

<script>
    function startTime() {
        var today=new Date();
        var h=today.getHours();
        var m=today.getMinutes();
        var s=today.getSeconds();
        m = checkTime(m);
        s = checkTime(s);
        document.getElementById('txt').innerHTML =  h+":"+m+":"+s;
        var t = setTimeout(function(){startTime()},500);
    }

    function checkTime(i) {
        if (i<10) {i = "0" + i};  // add zero in front of numbers < 10
        return i;
    }
</script>