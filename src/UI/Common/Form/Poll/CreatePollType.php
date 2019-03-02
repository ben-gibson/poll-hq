<?php

declare(strict_types=1);

namespace App\UI\Common\Form\Poll;

use App\Application\Command\Poll\CreatePoll;
use App\Domain\Poll\PollId;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreatePollType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('id', HiddenType::class, [
                'empty_data' => PollId::generate()->id(),
            ])
            ->add('name', TextType::class, ['attr' => ['autofocus' => true]])
            ->add('topic', TextType::class)
            ->add(
                'responseTarget',
                IntegerType::class,
                [
                    'required' => false,
                    'help'     => 'The poll will automatically close when the target is reached. Leave blank if you do not have a particular target.',
                ]
            )
            ->add(
                'closeDate',
                TextType::class,
                [
                    'required' => false,
                    'help'     => 'Leave blank if you do not want to poll to close by a specific date.',
                ]
            )
            ->add('description', TextareaType::class, ['required' => false])
            ->add('save', SubmitType::class, ['label' => 'Create']);
    }



    public function configureOptions(OptionsResolver $resolver) : void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(['data_class' => CreatePoll::class]);
    }
}
