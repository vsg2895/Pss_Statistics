<style>

    /*body {*/
    /*    min-height: 100vh;*/
    /*    background-color: #18181c;*/
    /*    display: flex;*/
    /*    align-items: center;*/
    /*    justify-content: center;*/
    /*}*/

    @import url('https://fonts.googleapis.com/css?family=Nova+Mono&display=swap');

    #newYear {
        font-family: 'Nova Mono', monospace;
        font-size: 2.5rem;
        text-transform: uppercase;
        width: 1109px;
        height: 365px;
        color: turquoise;
    }

    .char {
        offset-path: path('M.2 219.6c247-107 233.3 91.4 406.4 138.4C659.2 426.6 750.2 6.6 552.2.6 337.7-5.9 426.9 415 696.1 359.4c256.1-52.9 182.1-217.9 413.1-163.9');
        offset-distance: calc(var(--char-index) * 1.5rem);
        position: absolute;
        animation: loop 3500ms cubic-bezier(.62,.01,.42,1.01) infinite alternate calc(var(--char-index) * 10ms);
    }

    @keyframes loop {
        50% {
            offset-distance: calc((var(--char-index) * 2.5rem) + 700px);
            color: hotpink;
        }
        100% {
            offset-distance: calc((var(--char-index) * 1.5rem) + 1690px);
        }
    }
</style>



<h1 data-splitting id="newYear">Can’t stop looping</h1>


<script>
    Splitting({
        whitespace: true
    })
</script>
