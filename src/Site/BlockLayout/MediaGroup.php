<?php
namespace AgileThemeTools\Site\BlockLayout;

use AgileThemeTools\Form\Element\RegionMenuSelect;
use Omeka\Site\BlockLayout\AbstractBlockLayout;
use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Api\Representation\SitePageRepresentation;
use Omeka\Api\Representation\SitePageBlockRepresentation;
use Laminas\Form\FormElementManager as FormElementManager;
use Omeka\Entity\SitePageBlock;
use Omeka\File\ThumbnailManager as ThumbnailManager;
use Omeka\Stdlib\HtmlPurifier;
use Omeka\Stdlib\ErrorStore;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\View\Renderer\PhpRenderer;

class MediaGroup extends AbstractBlockLayout
{

    /**
     * @var HtmlPurifier
     */
    protected $htmlPurifier;
    /**
     * @var FormElementManager
     */
    protected $formElementManager;

    public function __construct(HtmlPurifier $htmlPurifier, FormElementManager $formElementManager, ThumbnailManager $thumbnailManager)
    {
        $this->htmlPurifier = $htmlPurifier;
        $this->formElementManager = $formElementManager;
    }

    public function getLabel()
    {
        return 'Media Group'; // @translate
    }

    public function onHydrate(SitePageBlock $block, ErrorStore $errorStore)
    {
        $data = $block->getData();
        $data['introduction'] = isset($data['introduction']) ? $this->htmlPurifier->purify($data['introduction']) : '';
        $data['title'] = isset($data['title']) ? $this->htmlPurifier->purify($data['title']): '';
        $block->setData($data);
    }

    public function prepareForm(PhpRenderer $view) {
        $view->headLink()->appendStylesheet($view->assetUrl('css/agile_theme_tools_admin_styles.css', 'AgileThemeTools'));
    }

    public function form(PhpRenderer $view, SiteRepresentation $site,
                         SitePageRepresentation $page = null, SitePageBlockRepresentation $block = null
    ) {

        $title = new Text("o:block[__blockIndex__][o:data][title]");
        $title->setAttribute('class', 'block-title');
        $title->setLabel('Media Group Title (optional)');

        $groupCaption = new Textarea("o:block[__blockIndex__][o:data][groupCaption]");
        $groupCaption->setLabel('Group Caption Text (optional)');
        $groupCaption->setAttribute('class', 'block-introduction full wysiwyg');
        $groupCaption->setAttribute('rows',20);

        $groupCaptionAlignment = new Select("o:block[__blockIndex__][o:data][groupCaptionAlignment]");
        $groupCaptionAlignment->setLabel('Select Group Caption Alignment');
        $groupCaptionAlignment->setValueOptions(['left' => 'left','center' => 'center', 'right' => 'right']);        
        $groupCaptionAlignment->setAttribute('value', 'left');

        if ($block) {
            $title->setAttribute('value',$block->dataValue('title'));
            $groupCaption->setAttribute('value', $block->dataValue('groupCaption'));
            $groupCaptionAlignment->setAttribute('value', $block->dataValue('groupCaptionAlignment'));
        }

        $html = $view->formRow($title);
        $html .= $view->blockAttachmentsForm($block);
        $html .= '<a href="#" class="collapse" aria-label="collapse"><h4>' . $view->translate('Media Group Options'). '</h4></a>';
        $html .= '<div class="collapsible">';
        $html .= $view->formRow($groupCaption);
        $html .= $view->formRow($groupCaptionAlignment);
        $html .= $view->blockThumbnailTypeSelect($block);
        $html .= '</div>';
        return $html;
    }

    public function prepareRender(PhpRenderer $view)
    {
        $view->headLink()->appendStylesheet($view->assetUrl('css/media-group.css', 'AgileThemeTools'));
    }


    public function render(PhpRenderer $view, SitePageBlockRepresentation $block)
    {
        $attachments = $block->attachments();

        $data = $block->data();
        list($scope, $region) = explode(':', $data['region']);

        $renderValues = [
            'block' => $block,
            'attachments' => $attachments,
            'title' => $data['title'],
            'groupCaption' => $data['groupCaption'],
            'groupCaptionAlignment' => $block->dataValue('groupCaptionAlignment', 'left'),
            'blockId' => $block->id(),
            'regionClass' => 'region-' . $region,
            'targetID' => '#' . $region,
            'thumbnailType' => $block->dataValue('thumbnail_type', 'medium')
        ];


        return $view->partial('common/block-layout/media-group', $renderValues);
    }
}
