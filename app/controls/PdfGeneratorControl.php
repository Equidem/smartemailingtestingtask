<?php

require_once __DIR__ . '/../../vendor/others/mpdf/mpdf.php';

/**
 * Control symplifying rendering of flash messages
 */
class PdfGeneratorControl extends \Nette\Application\UI\Control {

    private $data;
    private $imgName;

    public function render() {
        $template = $this->template;
        $template->setFile(__DIR__ . '/pdfGeneratorControl.latte');
        $template->render();
    }

    //Will read data from model and prepare them for rendering, data hardcoded for now
    private function prepareData() {
        $this->data = [
            'sent' => 204, // počet odeslaných
            'opened' => 76, // počet otevření unikátních
            'openedtotal' => 103, // počet otevření s duplicitami
            'clicked' => 43, // počet kliknutých
            'unsubscribed' => 0, // počet odhlášených
            'returned' => 0, // počet vrácených
            'name' => "ZZ - MZ webinář Mioweb pripominka - 09.10.2014 16:55:41",
            'id' => 514,
            'unopened' => 128, // počet neotevřených
            'unopened_perc' => 62.745098039215684, // počet neotevřených z odeslaných procentuálně
            'opened_perc' => 37.254901960784316, // počet otevřených procentuálně
            'clicked_perc' => 56.578947368421048, // počet klikačů z otevřených procentuálně
            'clicked_perc_abs' => 21.078431372549019, // počet klikačů z odeslaných procentuálně
            'unsubscribed_perc' => 0, // počet odhlášení z otevřených procentuálně
            'unsubscribed_perc_abs' => 0, // počet odhlášení z odeslaných procentuálně
            'returned_perc' => 0, // počet vrácených z odeslanýc procentuálně
            'start' => new Nette\DateTime('2014-10-09 16:55:41') // start rozesílky
        ];

        $this->data['unsubscribed_perc_abs'] = round($this->data['unsubscribed_perc_abs'], 2);
        $this->data['unsubscribed_perc'] = round($this->data['unsubscribed_perc'], 2);
        $this->data['returned_perc'] = round($this->data['returned_perc'], 2);
        $this->data['clicked_perc_abs'] = round($this->data['clicked_perc_abs'], 2);

        if ($this->data['openedtotal'] != 0) {
            $this->data['unique_opened_perc'] = round(($this->data['opened'] / $this->data['openedtotal']) * 100, 2);
        } else {
            $this->data['unique_opened_perc'] = 0;
        }
    }

    //Prepares pie chart for pdf in form of png image file
    private function preparePieChart() {
        //Parameters of result
        $trueWidth = 250 * 6 / 4;
        $trueHeight = 150 * 6 / 4;
        $heightOfLegendsRectangle = 25;
        
        $image = imagecreatetruecolor($trueWidth * 2, $trueHeight * 2);
        $antialiasedImage = imagecreatetruecolor($trueWidth, $trueHeight);
        
        //Percentage calculations
        $openedRate = $this->data['opened_perc'];
        $bounceRate = 0;
        $unsubscribedRate = $this->data['unsubscribed_perc'];
        $clickRate = $this->data['clicked_perc_abs'];
        $unopenedRate = $this->data['unopened_perc'];
        $total = $openedRate + $bounceRate + $unsubscribedRate + $clickRate + $unopenedRate;
        $openedRate = ($openedRate / $total) * 100;
        $bounceRate = ($bounceRate / $total) * 100;
        $clickRate = ($clickRate / $total) * 100;
        $unsubscribedRate = ($unsubscribedRate / $total) * 100;
        $unopenedRate = ($unopenedRate / $total) * 100;
        
        $white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
        $black = imagecolorallocate($image, 0x00, 0x00, 0x00);
        $blue = imagecolorallocate($image, 0x08, 0x92, 0xCD);
        $red = imagecolorallocate($image, 0xF0, 0x4B, 0x51);
        $orange = imagecolorallocate($image, 0xEF, 0xA9, 0x1F);
        $grey = imagecolorallocate($image, 0xB1, 0xB1, 0xB1);
        $green = imagecolorallocate($image, 0x7B, 0xAF, 0x42);

        //White background 
        imagefilledrectangle($image, 0, 0, $trueWidth * 2, $trueHeight * 2, $white);

        //Rendering of non zero pie chart pieces
        if ($openedRate) {
            imagefilledarc($image, $trueWidth / 2.5, $trueHeight, $trueWidth / 1.25, $trueWidth / 1.25, 0, $openedRate * 3.6, $green, IMG_ARC_PIE);
        }
        if ($bounceRate) {
            imagefilledarc($image, $trueWidth / 2.5, $trueHeight, $trueWidth / 1.25, $trueWidth / 1.25, $openedRate * 3.6, ($openedRate + $bounceRate) * 3.6, $orange, IMG_ARC_PIE);
        }
        if ($unsubscribedRate) {
            imagefilledarc($image, $trueWidth / 2.5, $trueHeight, $trueWidth / 1.25, $trueWidth / 1.25, ($openedRate + $bounceRate) * 3.6, ($openedRate + $bounceRate + $unsubscribedRate) * 3.6, $red, IMG_ARC_PIE);
        }
        if ($clickRate) {
            imagefilledarc($image, $trueWidth / 2.5, $trueHeight, $trueWidth / 1.25, $trueWidth / 1.25, ($openedRate + $bounceRate + $unsubscribedRate) * 3.6, ($openedRate + $bounceRate + $unsubscribedRate + $clickRate) * 3.6, $blue, IMG_ARC_PIE);
        }
        imagefilledarc($image, $trueWidth / 2.5, $trueHeight, $trueWidth / 1.25, $trueWidth / 1.25, ($openedRate + $bounceRate + $unsubscribedRate + $clickRate) * 3.6, 360, $grey, IMG_ARC_PIE);
        
        //Central white area of pie chart
        imagefilledarc($image, $trueWidth / 2.5, $trueHeight, ($trueWidth / 1.25) * 0.6, ($trueWidth / 1.25) * 0.6, 0, 360, $white, IMG_ARC_PIE);
        
        //Legends
        imagefilledrectangle($image, $trueWidth, $trueHeight / 4, $trueWidth + 30, $trueHeight / 4 + $heightOfLegendsRectangle, $green);
        imagefilledrectangle($image, $trueWidth, 2 * $trueHeight / 4, $trueWidth + 30, 2 * $trueHeight / 4 + $heightOfLegendsRectangle, $orange);
        imagefilledrectangle($image, $trueWidth, 3 * $trueHeight / 4, $trueWidth + 30, 3 * $trueHeight / 4 + $heightOfLegendsRectangle, $red);
        imagefilledrectangle($image, $trueWidth, 4 * $trueHeight / 4, $trueWidth + 30, 4 * $trueHeight / 4 + $heightOfLegendsRectangle, $blue);
        imagefilledrectangle($image, $trueWidth, 5 * $trueHeight / 4, $trueWidth + 30, 5 * $trueHeight / 4 + $heightOfLegendsRectangle, $grey);
        imagettftext($image, 12, 0, ($trueWidth * 7) / 6, $trueHeight / 4 + $heightOfLegendsRectangle * 2 / 3, $black, __DIR__ . '/../../www/css/fonts/OpenSansRegular.ttf', 'Míra otevření ('.Round($openedRate, 2).'%)');
        imagettftext($image, 12, 0, ($trueWidth * 7) / 6, 2 * $trueHeight / 4 + $heightOfLegendsRectangle * 2 / 3, $black, __DIR__ . '/../../www/css/fonts/OpenSansRegular.ttf', 'Bounce rate ('.Round($bounceRate, 2).'%)');
        imagettftext($image, 12, 0, ($trueWidth * 7) / 6, 3 * $trueHeight / 4 + $heightOfLegendsRectangle * 2 / 3, $black, __DIR__ . '/../../www/css/fonts/OpenSansRegular.ttf', 'Míra odhlášení ('.Round($unsubscribedRate, 2).'%)');
        imagettftext($image, 12, 0, ($trueWidth * 7) / 6, 4 * $trueHeight / 4 + $heightOfLegendsRectangle * 2 / 3, $black, __DIR__ . '/../../www/css/fonts/OpenSansRegular.ttf', 'Míra prokliku ('.Round($clickRate, 2).'%)');
        imagettftext($image, 12, 0, ($trueWidth * 7) / 6, 5 * $trueHeight / 4 + $heightOfLegendsRectangle * 2 / 3, $black, __DIR__ . '/../../www/css/fonts/OpenSansRegular.ttf', 'Neotevřeno ('.Round($unopenedRate, 2).'%)');

        //Anti-aliasing
        imagecopyresampled($antialiasedImage, $image, 0, 0, 0, 0, $trueWidth, $trueHeight, $trueWidth * 2, $trueHeight * 2);
        imagedestroy($image);
        
        $this->imgName = __DIR__ . '/../../www/images/'.$this->data['id'].'.png';
        
        imagepng($antialiasedImage, $this->imgName, 0);
        
        imagedestroy($antialiasedImage);
        $this->data['image'] = $this->data['id'].'.png';
    }

    //Prepare PDF file and send it to user
    private function renderPDF() {
        $latteEngine = new \Latte\Engine();

        $this->preparePieChart();

        $templateParameters = $this->data;

        $headerTemplateParameters = array();
        $headerTemplateParameters['statisticsURL'] = "URL ADRESA NA STATISTIKU V APLIKACI";

        $htmlVersion = $latteEngine->renderToString(__DIR__ . '/pdfTemplate.latte', $templateParameters);
        $htmlHeader = $latteEngine->renderToString(__DIR__ . '/pdfHeader.latte', $headerTemplateParameters);

        $pdfGenerator = new \mPDF('', 'A4', 0, '', 15, 15, 30, 30, 9, 9);

        //Include stylesheet
        $pdfGenerator->WriteHTML(file_get_contents(__DIR__ . '/../../www/css/pdf.css'), 1);

        // Footer is same as header
        $pdfGenerator->SetHTMLHeader($htmlHeader);
        $pdfGenerator->SetHTMLFooter($htmlHeader);

        $pdfGenerator->WriteHTML($htmlVersion, 2);
        
        //Delete pie chart so as not to flood memory
        unlink($this->imgName);
        
        //Outputting to browser now, later will be sent as file
        $pdfGenerator->Output();
    }

    //Prepare PDF file and send it to user
    private function sendPDFVersionToUser() {
        //Read data from model
        $this->prepareData();
        
        //Use data to render PDF and send it to user
        $this->renderPDF();
    }

    public function createComponentGenerateOptionsForm() {
        $form = new \Nette\Application\UI\Form();

        $form->addSubmit('generateSubmit', 'Generovat');

        $form->onSuccess[] = $this->onSuccessGenerateOptionsForm;

        return $form;
    }

    public function onSuccessGenerateOptionsForm() {
        //Options should apply here, but there are none yet
        $this->sendPDFVersionToUser();
        $this->getPresenter()->redirect('this');
    }

}
