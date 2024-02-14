<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intranet/public/about/calendar.php");

?>


    <h1>Создание элемента CRM</h1>

    <form id="myForm" action="sender.php" method="POST">
        <label for="title">Название элемента:</label>
        <input type="text" id="title" name="name" required><br>

        <label for="username">Имя контакта</label>
        <input type="text" id="username" name="tel" required><br>

        <label for="sum">Сумма</label>
        <input type="text" id="sum" name="comment" required><br>

        <label for="parentId">Идентификатор родительского элемента:</label>
        <input type="text" id="parentId" name="parentId" required><br>

        <button type="submit">Сохранить</button>
    </form>

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" >
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(93801183, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true
        });
    </script>

    <noscript><div><img src="https://mc.yandex.ru/watch/93801183" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>