# Generated by Django 4.1.6 on 2023-02-25 12:43

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('mainapp', '0008_alter_article_status'),
    ]

    operations = [
        migrations.AddField(
            model_name='article',
            name='ordinal_number',
            field=models.IntegerField(default=0, verbose_name='Tartib raqami:'),
        ),
        migrations.AlterField(
            model_name='article',
            name='status',
            field=models.CharField(choices=[('open', 'Barcha uchun ochiq'), ('close', 'Barcha uchun yopiq')], default='close', max_length=18, verbose_name='Holati'),
        ),
    ]