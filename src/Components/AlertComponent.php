<?php
namespace App\Components;

use App\Repository\ProductRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;
use Symfony\UX\TwigComponent\Attribute\PostMount;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('alert',template: 'comp/alert.html.twig',attributesVar: 'hamza')]
class AlertComponent
{
//    private ProductRepository $productRepository;
//
//    public function __construct(ProductRepository $productRepository)
//    {
//        $this->productRepository = $productRepository;
//    }
//    public function getProducts(): array
//    {
//        return $this->productRepository->findAll();
//    }
//    #[ExposeInTemplate('hamza')]
//    #[ExposeInTemplate(name: 'hamza', getter: 'getHamza')]
//    public string $message;
//    public string $type = 'success';

//    public string $hamza;
   /* public function mount(bool $isSuccess = true)
    {
        $this->type = $isSuccess ? 'success' : 'danger';
    }*/


    /*public function getHamza(): string
    {
        return $this->message;
    }

    public function setMessage(string $value): string
    {
        return $this->message = $value;
    }*/

//    #[PreMount]
//    public function preMount(array $data): array
//    {
//        // validate data
//        $resolver = new OptionsResolver();
//        $resolver->setDefaults(['type' => 'success']);
//        $resolver->setAllowedValues('type', ['success', 'danger','info']);
//        $resolver->setRequired('message');
//        $resolver->setAllowedTypes('message', 'string');
////        $resolver->setRequired('hamza');
//        return $resolver->resolve($data);
//    }

}