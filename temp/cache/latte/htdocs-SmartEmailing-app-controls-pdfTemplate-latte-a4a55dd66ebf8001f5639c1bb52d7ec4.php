<?php
// source: /opt/lampp/htdocs/SmartEmailing/app/controls/pdfTemplate.latte

// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('0799325018', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lbfad18cd22b_content')) { function _lbfad18cd22b_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><div id="pageContent">
    <h1>Export statistiky kampaně XYZ</h1>
    <br>
    <u>Základní informace</u>
    <br>
    <p>
        Odesláno emailů: <?php echo Latte\Runtime\Filters::escapeHtml($sent, ENT_NOQUOTES) ?><br>
        Datum rozesílky:<br>
        Název kampaně:<br>
        Předmět kampaně:<br>
        Odesílatel:<br>
        Adresa pro odpovědi:<br>
        Odesláno na seznamy:<br>
        Vyloučené seznamy z rozesílky:<br>
    </p>
    <br>
    
    <u>Přehled otevřených</u>
    <br>
    <p>
        Počet unikátních otevření: <?php echo Latte\Runtime\Filters::escapeHtml($unique_opened_perc, ENT_NOQUOTES) ?>%<br>
        Počet celkových otevření: <?php echo Latte\Runtime\Filters::escapeHtml($opened, ENT_NOQUOTES) ?><br>
    </p>
    <br>
    <u>Přehled o klikání</u>
    <br>
    <p>
        Celkem kliknuto: <?php echo Latte\Runtime\Filters::escapeHtml($clicked, ENT_NOQUOTES) ?><br>
        Click rate: <br>
        Kliklo a otevřelo: <br>
    </p>
    <br>
    <br>
    <u>Přehled odhlášených</u>
    <br>
    <p>
        Počet odhlášených: <?php echo Latte\Runtime\Filters::escapeHtml($unsubscribed_perc_abs, ENT_NOQUOTES) ?>%<br>
        Počet odhlášených v % oproti opens: <?php echo Latte\Runtime\Filters::escapeHtml($unsubscribed_perc, ENT_NOQUOTES) ?>%<br>
    </p>
    <br>
    <u>Přehled o vrácených</u>
    <br>
    Počet vrácených: <?php echo Latte\Runtime\Filters::escapeHtml($returned_perc, ENT_NOQUOTES) ?>%
</div>
<?php
}}

//
// end of blocks
//

// template extending

$_l->extends = empty($_g->extended) && isset($_control) && $_control instanceof Nette\Application\UI\Presenter ? $_control->findLayoutTemplateFile() : NULL; $_g->extended = TRUE;

if ($_l->extends) { ob_start();}

// prolog Nette\Bridges\ApplicationLatte\UIMacros

// snippets support
if (empty($_l->extends) && !empty($_control->snippetMode)) {
	return Nette\Bridges\ApplicationLatte\UIMacros::renderSnippets($_control, $_b, get_defined_vars());
}

//
// main template
//
if ($_l->extends) { ob_end_clean(); return $template->renderChildTemplate($_l->extends, get_defined_vars()); }
call_user_func(reset($_b->blocks['content']), $_b, get_defined_vars()) ; 