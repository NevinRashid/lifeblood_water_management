<?php

namespace Modules\Sensors\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\DistributionNetwork\Models\Valve;
use Modules\Sensors\Models\SensorReading;
use Modules\Sensors\Notifications\AbnormalSensorReading;

class SendAbnormalReading implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public SensorReading $reading,
        public string $abnormalityType,
    ) {}

    /**
     * Execute the job.
     */
    public function handle()
    {
        // الحصول على الـ Valve المرتبط بالقراءة عبر الـ Sensor
        $x = $this->reading->sensor->sensorable;
        

        // الحصول على شبكة التوزيع المرتبطة بالـ Valve
        $distributionNetwork = $x->network;

        if ($distributionNetwork) {
            // الحصول على مدير هذه الشبكة
            $manager = $distributionNetwork->manager;

            // إرسال الإشعار فقط إلى المدير المرتبط

            $manager->notify(
                new AbnormalSensorReading(
                    $this->reading,
                    $this->abnormalityType
                )
            );
        }
    }
}
