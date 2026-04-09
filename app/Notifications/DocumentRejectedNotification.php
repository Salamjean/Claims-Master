<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Sinistre;
use App\Models\SinistreDocumentAttendu;

class DocumentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $sinistre;
    protected $documentAttendu;
    protected $feedback;

    /**
     * Create a new notification instance.
     */
    public function __construct(Sinistre $sinistre, SinistreDocumentAttendu $documentAttendu, ?string $feedback)
    {
        $this->sinistre = $sinistre;
        $this->documentAttendu = $documentAttendu;
        $this->feedback = $feedback;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Un document a été rejeté - Sinistre N°' . $this->sinistre->id)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Nous vous informons qu\'un document soumis pour votre déclaration de sinistre ("' . str_replace('_', ' ', $this->sinistre->type_sinistre) . '") a été refusé par nos services.')
            ->line('**Document concerné :** ' . $this->documentAttendu->nom_document);

        if ($this->feedback) {
            $mail->line('**Motif du rejet / Observation du gestionnaire :**')
                ->line($this->feedback);
        }

        $mail->action('Soumettre un nouveau document', url('/mon-espace/sinistres/' . $this->sinistre->id . '/upload-docs'))
            ->line('Merci de vous connecter à votre espace personnel pour uploader une version valide de cette pièce justificative.')
            ->line('L\'équipe CLAIMS MASTER.');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'sinistre_id' => $this->sinistre->id,
            'document' => $this->documentAttendu->nom_document,
            'message' => 'Un document a été rejeté par l\'assurance.'
        ];
    }
}
