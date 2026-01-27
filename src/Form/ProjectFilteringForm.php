<?php

namespace App\Form;

use App\Entity\ProjectFiltering;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectFilteringForm extends AbstractType
{
    /**
     * Initialise les composants du formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'required' => false,
            'label' => false,
        ]);
    }

    /**
     * Initialise la configuration du formulaire.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectFiltering::class,
            'method' => 'get',
            'csrf_protection' => false,
        ]);
    }
}
