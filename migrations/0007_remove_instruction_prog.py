# Generated by Django 4.0.4 on 2022-06-12 16:25

from django.db import migrations


class Migration(migrations.Migration):

    dependencies = [
        ('orders', '0006_instruction_prog'),
    ]

    operations = [
        migrations.RemoveField(
            model_name='instruction',
            name='prog',
        ),
    ]
