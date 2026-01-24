<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    /**
     * Initialise les composants du formulaire.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Nom'
            ])
            ->add('description', TextType::class, [
                'required' => true,
                'label' => 'Description'
            ])
            ->add('startingDate', DateType::class, [
                'required' => true,
                'label' => 'Date de début'
            ])
            ->add('endingDate', DateType::class, [
                'required' => false,
                'label' => 'Date de fin'
            ])
        ;
    }

    /**
     * Initialise la configuration du formulaire.
     *
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
