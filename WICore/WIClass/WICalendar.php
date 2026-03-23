<?php
declare(strict_types=1);

#[\AllowDynamicProperties]
class WICalendar
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function getEvents(): array
    {
        return $this->WIdb->bindfree(
            "SELECT * FROM wi_calendar ORDER BY event_date ASC"
        );
    }

    public function render(): void
    {
        $events = $this->getEvents();

        echo '<div class="wi-calendar">';

        foreach ($events as $event) {

            $date = htmlspecialchars($event['event_date']);
            $title = htmlspecialchars($event['title']);
            $desc = htmlspecialchars($event['description']);

            echo '<div class="calendar-event">
                    <strong>' . $date . '</strong>
                    <h4>' . $title . '</h4>
                    <p>' . $desc . '</p>
                  </div>';
        }

        echo '</div>';
    }
}
?>