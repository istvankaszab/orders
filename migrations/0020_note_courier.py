# Generated by Django 4.0.4 on 2022-06-25 18:16

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('orders', '0019_note_order'),
    ]

    operations = [
        migrations.AddField(
            model_name='note',
            name='courier',
            field=models.BooleanField(default=False),
        ),
    ]
