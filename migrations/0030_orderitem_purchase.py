# Generated by Django 4.0.5 on 2022-07-12 19:21

from django.db import migrations, models
import django.db.models.deletion


class Migration(migrations.Migration):

    dependencies = [
        ('orders', '0029_purchase'),
    ]

    operations = [
        migrations.AddField(
            model_name='orderitem',
            name='purchase',
            field=models.ForeignKey(blank=True, null=True, on_delete=django.db.models.deletion.PROTECT, to='orders.purchase'),
        ),
    ]
