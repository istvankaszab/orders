# Generated by Django 4.0.4 on 2022-06-25 18:14

from django.db import migrations, models
import django.db.models.deletion


class Migration(migrations.Migration):

    dependencies = [
        ('orders', '0018_rename_instruction_note'),
    ]

    operations = [
        migrations.AddField(
            model_name='note',
            name='order',
            field=models.ForeignKey(default=1, on_delete=django.db.models.deletion.CASCADE, to='orders.order'),
            preserve_default=False,
        ),
    ]
