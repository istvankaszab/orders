# Generated by Django 4.0.5 on 2022-06-12 16:23

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('orders', '0004_orderitem_quantity'),
    ]

    operations = [
        migrations.AddField(
            model_name='instruction',
            name='text',
            field=models.TextField(default='aaa'),
            preserve_default=False,
        ),
    ]
