<?php

namespace App\Controller\Admin;

use App\Entity\Video;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class VideoCrudController extends AbstractCrudController
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Video::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title');
        yield DateTimeField::new('postDate');
        yield TextField::new('videoUrl');
        yield TextField::new('posterUrl');
        yield BooleanField::new('isPremium');
        yield BooleanField::new('isHeader');
        yield AssociationField::new('tag');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['postDate' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $deleteAction = Action::new('deleteVideo', 'Delete')
            ->linkToUrl(function (Video $video) {
                $url = $this->urlGenerator->generate('delete_video', ['idVideo' => $video->getId()]);
                return $url;
            })
            ->addCssClass('text-danger');

        return $actions->add(Crud::PAGE_INDEX, $deleteAction)
        ->remove(Crud::PAGE_INDEX, Action::DELETE)
        ->remove(Crud::PAGE_DETAIL, Action::DELETE);
    }
}
